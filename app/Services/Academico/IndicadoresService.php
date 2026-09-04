<?php
namespace App\Services\Academico;

use App\Models\Alumno;
use App\Models\Baja;
use App\Models\Calificacion;
use Illuminate\Support\Facades\DB;

/**
 * IndicadoresService — §30, §74
 * Calcula indicadores de permanencia y estadísticas académicas.
 */
class IndicadoresService
{
    public function calcularIndicadoresSede(int $sedeId, int $cicloId): array
    {
        $inscritos  = Alumno::where('sede_actual_id',$sedeId)->where('estatus','activo')->count();
        $bajasTmp   = Baja::where('tipo','temporal')->whereHas('alumno',fn($q)=>$q->where('sede_actual_id',$sedeId))->where('estado','activa')->count();
        $bajasDef   = Baja::where('tipo','definitiva')->whereHas('alumno',fn($q)=>$q->where('sede_actual_id',$sedeId))->count();
        $deserciones= Baja::where('tipo','desercion')->whereHas('alumno',fn($q)=>$q->where('sede_actual_id',$sedeId))->count();
        $traslados  = Baja::where('tipo','traslado')->whereHas('alumno',fn($q)=>$q->where('sede_actual_id',$sedeId))->count();
        $egresados  = Alumno::where('sede_actual_id',$sedeId)->where('estatus','egresado')->count();

        $totalCalificaciones = Calificacion::where('ciclo_escolar_id',$cicloId)
            ->whereHas('alumno',fn($q)=>$q->where('sede_actual_id',$sedeId))
            ->whereNotNull('resultado')->count();

        $aprobadas = Calificacion::where('ciclo_escolar_id',$cicloId)
            ->whereHas('alumno',fn($q)=>$q->where('sede_actual_id',$sedeId))
            ->where('resultado','aprobado')->count();

        $reprobadas = $totalCalificaciones > 0 ? $totalCalificaciones - $aprobadas : 0;
        $totalHist  = $inscritos + $bajasDef + $deserciones + $traslados;

        return [
            'inscritos'           => $inscritos,
            'bajas_temporales'    => $bajasTmp,
            'bajas_definitivas'   => $bajasDef,
            'deserciones'         => $deserciones,
            'traslados'           => $traslados,
            'egresados'           => $egresados,
            'pct_aprobacion'      => $totalCalificaciones > 0 ? round(($aprobadas/$totalCalificaciones)*100,1) : null,
            'pct_reprobacion'     => $totalCalificaciones > 0 ? round(($reprobadas/$totalCalificaciones)*100,1) : null,
            'pct_desercion'       => $totalHist > 0 ? round(($deserciones/$totalHist)*100,1) : null,
            'pct_permanencia'     => $totalHist > 0 ? round(($inscritos/$totalHist)*100,1) : null,
            'pct_egreso'          => $totalHist > 0 ? round(($egresados/$totalHist)*100,1) : null,
        ];
    }
}
