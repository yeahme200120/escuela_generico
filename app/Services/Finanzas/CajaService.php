<?php

namespace App\Services\Finanzas;

use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\TurnoCaja;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\DB;

class CajaService
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    /**
     * Abre un turno de caja. Lanza excepción si ya hay un turno abierto para esa caja.
     */
    public function abrir(int $cajaId, int $userId, float $montoApertura = 0): TurnoCaja
    {
        $turnoActivo = TurnoCaja::where('caja_id', $cajaId)
            ->where('estado', 'abierto')
            ->first();

        if ($turnoActivo) {
            throw new \RuntimeException('La caja ya tiene un turno abierto.');
        }

        $turno = TurnoCaja::create([
            'caja_id'        => $cajaId,
            'usuario_id'     => $userId,
            'fecha_apertura' => now(),
            'monto_apertura' => $montoApertura,
            'estado'         => 'abierto',
        ]);

        $this->audit->log(
            modulo:      'caja',
            accion:      'open',
            descripcion: "Turno de caja abierto #{$turno->id} (caja {$cajaId})",
            model:       TurnoCaja::class,
            modelId:     $turno->id,
        );

        return $turno;
    }

    /**
     * Cierra un turno de caja calculando diferencias.
     */
    public function cerrar(TurnoCaja $turno, float $montoCierre, string $observaciones = ''): void
    {
        if (!$turno->estaAbierto()) {
            throw new \RuntimeException('El turno no está abierto.');
        }

        // Calcular monto esperado a partir de los movimientos
        $totalIngresos = $turno->movimientos()
            ->where('tipo', 'ingreso')
            ->sum('importe');

        $totalEgresos = $turno->movimientos()
            ->whereIn('tipo', ['egreso', 'retiro'])
            ->sum('importe');

        $montoEsperado = (float) $turno->monto_apertura + $totalIngresos - $totalEgresos;
        $diferencia    = $montoCierre - $montoEsperado;

        DB::transaction(function () use ($turno, $montoCierre, $montoEsperado, $diferencia, $observaciones) {
            $turno->update([
                'fecha_cierre'   => now(),
                'estado'         => 'cerrado',
                'monto_cierre'   => $montoCierre,
                'monto_esperado' => $montoEsperado,
                'diferencia'     => $diferencia,
                'observaciones'  => $observaciones,
            ]);

            $this->audit->log(
                modulo:  'caja',
                accion:  'close',
                model:   TurnoCaja::class,
                modelId: $turno->id,
                after:   [
                    'estado'         => 'cerrado',
                    'monto_cierre'   => $montoCierre,
                    'monto_esperado' => $montoEsperado,
                    'diferencia'     => $diferencia,
                ],
            );
        });
    }

    /**
     * Registra un movimiento de caja. El turno debe estar abierto.
     */
    public function registrarMovimiento(
        TurnoCaja $turno,
        string    $tipo,
        string    $concepto,
        float     $importe,
        ?int      $pagoId = null,
        int       $userId = 0,
    ): MovimientoCaja {
        if (!$turno->estaAbierto()) {
            throw new \RuntimeException('El turno no está abierto.');
        }

        return MovimientoCaja::create([
            'turno_caja_id' => $turno->id,
            'tipo'          => $tipo,
            'concepto'      => $concepto,
            'importe'       => $importe,
            'pago_id'       => $pagoId,
            'usuario_id'    => $userId ?: null,
            'created_at'    => now(),
        ]);
    }
}
