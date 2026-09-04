<?php
namespace App\Services\Academico;

use App\Models\Calificacion;
use App\Models\PeriodoEvaluacion;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\DB;

/**
 * CalificacionService — §41
 */
class CalificacionService
{
    public function __construct(private readonly AuditService $audit) {}

    /**
     * Registra o actualiza una calificación.
     * Si el periodo está cerrado, requiere permiso especial (verificado antes de llamar).
     */
    public function registrar(array $datos, int $userId): Calificacion
    {
        $periodo = PeriodoEvaluacion::findOrFail($datos['periodo_evaluacion_id']);

        if ($periodo->cerrado) {
            // El controller debe haber verificado permiso 'calificaciones.autorizar' antes
            // Solo registramos con auditoría extra
        }

        return DB::transaction(function() use ($datos, $userId, $periodo) {
            $antes = Calificacion::where([
                'alumno_id'=>$datos['alumno_id'],
                'materia_id'=>$datos['materia_id'],
                'periodo_evaluacion_id'=>$datos['periodo_evaluacion_id'],
            ])->first()?->toArray();

            $cal = Calificacion::updateOrCreate(
                ['alumno_id'=>$datos['alumno_id'],'materia_id'=>$datos['materia_id'],'periodo_evaluacion_id'=>$datos['periodo_evaluacion_id']],
                [
                    'grupo_id'          => $datos['grupo_id'],
                    'docente_id'        => $datos['docente_id'] ?? null,
                    'ciclo_escolar_id'  => $datos['ciclo_escolar_id'],
                    'calificacion'      => $datos['calificacion'],
                    'resultado'         => $this->determinarResultado($datos['calificacion'], $datos['minimo'] ?? 6.0),
                    'usuario_registra'  => $antes ? $datos['usuario_registra'] ?? $userId : $userId,
                    'usuario_actualiza' => $userId,
                    'observaciones'     => $datos['observaciones'] ?? null,
                ]
            );

            $this->audit->log(
                modulo:'calificaciones', accion: $antes ? 'update' : 'create',
                model:Calificacion::class, modelId:$cal->id,
                before: $antes ?? [], after: $cal->toArray(),
                descripcion:"Cal. alumno#{$datos['alumno_id']} materia#{$datos['materia_id']} = {$datos['calificacion']}",
                permiso: $periodo->cerrado ? 'calificaciones.autorizar' : 'calificaciones.registrar'
            );

            return $cal;
        });
    }

    /**
     * Cierra un periodo de evaluación. §41
     */
    public function cerrarPeriodo(PeriodoEvaluacion $periodo, int $userId): void
    {
        $periodo->update(['cerrado'=>true,'cerrado_at'=>now(),'cerrado_por'=>$userId]);
        $this->audit->log(modulo:'calificaciones',accion:'close',model:PeriodoEvaluacion::class,modelId:$periodo->id,descripcion:"Periodo cerrado: {$periodo->nombre}");
    }

    private function determinarResultado(?float $cal, float $minimo): string
    {
        if ($cal === null) return 'na';
        return $cal >= $minimo ? 'aprobado' : 'reprobado';
    }
}
