<?php
namespace App\Services\Alumnos;

use App\Models\Alumno;
use App\Models\Baja;
use App\Models\Reingreso;
use App\Models\TrayectoriaAlumno;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * BajaService — §24–§27
 * Gestiona bajas temporales, definitivas, deserciones, traslados y reingresos.
 * NUNCA elimina físicamente al alumno.
 */
class BajaService
{
    public function __construct(private readonly AuditService $audit) {}

    public function registrarBaja(Alumno $alumno, array $datos, int $userId): Baja
    {
        return DB::transaction(function() use ($alumno, $datos, $userId) {
            $baja = Baja::create(array_merge($datos, [
                'alumno_id'       => $alumno->id,
                'usuario_solicita'=> $userId,
                'estatus'         => 'solicitada',
            ]));

            // Actualizar trayectoria activa
            TrayectoriaAlumno::where('alumno_id',$alumno->id)->whereNull('fecha_fin')
                ->update(['estatus'=>$datos['tipo'],'fecha_fin'=>now(),'motivo'=>$datos['motivo']]);

            // Actualizar estatus del alumno
            $nuevoEstatus = match($datos['tipo']) {
                'temporal'   => 'baja_temporal',
                'definitiva','desercion','traslado' => 'baja_definitiva',
                'egreso'     => 'egresado',
                default      => 'baja_definitiva',
            };
            $alumno->withoutAudit(fn($a) => $a->update(['estatus' => $nuevoEstatus]));

            $this->audit->log(modulo:'control_escolar',accion:'baja',model:Alumno::class,modelId:$alumno->id,
                descripcion:"Baja {$datos['tipo']}: {$alumno->nombre_completo}",motivo:$datos['motivo']);

            return $baja;
        });
    }

    public function procesarReingreso(Alumno $alumno, array $datos, int $userId): Reingreso
    {
        return DB::transaction(function() use ($alumno, $datos, $userId) {
            $reingreso = Reingreso::create(array_merge($datos, [
                'alumno_id'  => $alumno->id,
                'usuario_id' => $userId,
                'estado'     => 'aprobado',
            ]));

            // Reactivar alumno
            $alumno->withoutAudit(fn($a) => $a->update([
                'estatus'               => 'activo',
                'situacion_inscripcion' => 'reinscrito',
                'sede_actual_id'        => $datos['sede_id'],
            ]));

            // Nueva trayectoria (preservando la anterior)
            TrayectoriaAlumno::create([
                'alumno_id'          => $alumno->id,
                'ciclo_escolar_id'   => $datos['ciclo_escolar_id'],
                'sede_id'            => $datos['sede_id'],
                'grado_id'           => $datos['grado_id'],
                'grupo_id'           => $datos['grupo_id'] ?? null,
                'estatus'            => 'reinscrito',
                'situacion_academica'=> 'regular',
                'fecha_inicio'       => $datos['fecha_reingreso'] ?? now(),
                'usuario_id'         => $userId,
            ]);

            $this->audit->log(modulo:'control_escolar',accion:'reingreso',model:Alumno::class,modelId:$alumno->id,
                descripcion:"Reingreso: {$alumno->nombre_completo}");

            return $reingreso;
        });
    }
}
