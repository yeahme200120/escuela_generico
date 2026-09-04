<?php
namespace App\Services\Academico;

use App\Models\Asistencia;
use App\Models\Justificante;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\DB;

/**
 * AsistenciaService — §39–§40
 */
class AsistenciaService
{
    public function __construct(private readonly AuditService $audit) {}

    /**
     * Registra lista de asistencia de un grupo en una fecha.
     * $lista = [['alumno_id'=>1,'estado'=>'presente'],...]
     */
    public function registrarLista(int $grupoId, int $materiaId, int $docenteId, int $cicloId, string $fecha, array $lista, int $userId): void
    {
        DB::transaction(function() use ($grupoId,$materiaId,$docenteId,$cicloId,$fecha,$lista,$userId) {
            foreach ($lista as $item) {
                Asistencia::updateOrCreate(
                    ['alumno_id'=>$item['alumno_id'],'grupo_id'=>$grupoId,'materia_id'=>$materiaId,'fecha'=>$fecha,'ciclo_escolar_id'=>$cicloId],
                    ['docente_id'=>$docenteId,'estado'=>$item['estado'],'hora_registro'=>now()->toTimeString(),'registrado_por'=>$userId]
                );
            }
        });
        $this->audit->log(modulo:'asistencias',accion:'registrar_lista',descripcion:"Pase grupo#{$grupoId} {$fecha}");
    }

    /**
     * Aplica un justificante aprobado — NO modifica el registro de asistencia original. §40
     */
    public function aplicarJustificante(Justificante $justificante, int $userId): void
    {
        if ($justificante->estado !== 'pendiente') {
            throw new \RuntimeException('El justificante no está pendiente de aprobación.');
        }
        DB::transaction(function() use ($justificante, $userId) {
            $justificante->update(['estado'=>'aprobado','autorizado_por'=>$userId]);
            // Crear asistencia tipo justificada (registro adicional, no modifica el original)
            Asistencia::where('alumno_id', $justificante->alumno_id)
                ->whereBetween('fecha', [$justificante->fecha_inicio, $justificante->fecha_fin])
                ->where('estado','falta')
                ->update(['estado'=>'justificada']);
        });
        $this->audit->log(modulo:'asistencias',accion:'justify',model:Justificante::class,modelId:$justificante->id);
    }

    /**
     * Calcula % de asistencia de un alumno en un ciclo. §43
     */
    public function calcularPorcentaje(int $alumnoId, int $cicloId): float
    {
        $total = Asistencia::where('alumno_id',$alumnoId)->where('ciclo_escolar_id',$cicloId)->count();
        if ($total === 0) return 1.0;
        $presentes = Asistencia::where('alumno_id',$alumnoId)->where('ciclo_escolar_id',$cicloId)
            ->whereIn('estado',['presente','retardo','justificada'])->count();
        return round($presentes / $total, 4);
    }
}
