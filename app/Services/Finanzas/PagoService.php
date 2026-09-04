<?php

namespace App\Services\Finanzas;

use App\Models\Cargo;
use App\Models\Pago;
use App\Models\PagoDetalle;
use App\Models\Parcialidad;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\DB;

class PagoService
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    /**
     * Registra un pago aplicando idempotencia.
     *
     * $data esperado:
     *   alumno_id, sede_id, caja_id?, referencia?, importe, fecha_pago,
     *   metodo_pago_id?, request_id?,
     *   cargos: array de [cargo_id, importe_aplicado, parcialidad_id?]
     */
    public function registrar(array $data, int $userId): Pago
    {
        // ── Idempotencia ──────────────────────────────────────────────
        // Se usa el primer cargo del array para construir la clave
        $primerCargo = $data['cargos'][0]['cargo_id'] ?? 0;
        $idempotencyKey = hash('sha256', $data['alumno_id'] . '|' . $primerCargo . '|' . $data['fecha_pago']);

        $existente = Pago::where('idempotency_key', $idempotencyKey)
            ->where('estado', 'activo')
            ->first();

        if ($existente) {
            return $existente;
        }

        return DB::transaction(function () use ($data, $userId, $idempotencyKey) {
            // ── Crear pago ────────────────────────────────────────────
            $pago = Pago::create([
                'alumno_id'       => $data['alumno_id'],
                'sede_id'         => $data['sede_id'],
                'caja_id'         => $data['caja_id'] ?? null,
                'referencia'      => $data['referencia'] ?? null,
                'importe'         => $data['importe'],
                'fecha_pago'      => $data['fecha_pago'],
                'metodo_pago_id'  => $data['metodo_pago_id'] ?? null,
                'usuario_id'      => $userId,
                'estado'          => 'activo',
                'idempotency_key' => $idempotencyKey,
                'request_id'      => $data['request_id'] ?? null,
            ]);

            // ── Aplicar a cargos ──────────────────────────────────────
            foreach ($data['cargos'] as $item) {
                $cargo = Cargo::findOrFail($item['cargo_id']);

                PagoDetalle::create([
                    'pago_id'          => $pago->id,
                    'cargo_id'         => $cargo->id,
                    'parcialidad_id'   => $item['parcialidad_id'] ?? null,
                    'importe_aplicado' => $item['importe_aplicado'],
                ]);

                // Recalcular estado del cargo
                $totalAplicado = PagoDetalle::where('cargo_id', $cargo->id)
                    ->join('pagos', 'pagos.id', '=', 'pago_detalle.pago_id')
                    ->where('pagos.estado', 'activo')
                    ->sum('pago_detalle.importe_aplicado');

                $nuevoEstado = $totalAplicado >= $cargo->total ? 'pagado' : 'parcial';
                $cargo->update(['estado' => $nuevoEstado]);

                // Actualizar parcialidad si aplica
                if (!empty($item['parcialidad_id'])) {
                    Parcialidad::where('id', $item['parcialidad_id'])
                        ->update(['estado' => 'pagado']);
                }
            }

            // ── Auditoría ─────────────────────────────────────────────
            $this->audit->log(
                modulo:      'finanzas',
                accion:      'create',
                descripcion: "Pago registrado #{$pago->id}",
                model:       Pago::class,
                modelId:     $pago->id,
            );

            return $pago;
        });
    }

    /**
     * Cancela un pago activo y revierte los estados de los cargos.
     */
    public function cancelar(Pago $pago, string $motivo, int $userId): void
    {
        if ($pago->estado !== 'activo') {
            throw new \RuntimeException('Pago no cancelable');
        }

        DB::transaction(function () use ($pago, $motivo, $userId) {
            // ── Cancelar el pago ──────────────────────────────────────
            $pago->update([
                'estado'              => 'cancelado',
                'motivo_cancelacion'  => $motivo,
                'cancelado_por'       => $userId,
                'cancelado_at'        => now(),
            ]);

            // ── Revertir cargos afectados ─────────────────────────────
            $cargoIds = PagoDetalle::where('pago_id', $pago->id)
                ->pluck('cargo_id')
                ->unique();

            foreach ($cargoIds as $cargoId) {
                $cargo = Cargo::find($cargoId);
                if (!$cargo) continue;

                // Recalcular total aún activo después de la cancelación
                $totalAplicado = PagoDetalle::where('cargo_id', $cargoId)
                    ->join('pagos', 'pagos.id', '=', 'pago_detalle.pago_id')
                    ->where('pagos.estado', 'activo')
                    ->sum('pago_detalle.importe_aplicado');

                $nuevoEstado = match (true) {
                    $totalAplicado <= 0              => 'pendiente',
                    $totalAplicado < $cargo->total   => 'parcial',
                    default                          => 'pagado',
                };

                $cargo->update(['estado' => $nuevoEstado]);
            }

            // ── Auditoría ─────────────────────────────────────────────
            $this->audit->log(
                modulo:  'finanzas',
                accion:  'cancel',
                model:   Pago::class,
                modelId: $pago->id,
                before:  ['estado' => 'activo'],
                after:   ['estado' => 'cancelado'],
                motivo:  $motivo,
            );
        });
    }
}
