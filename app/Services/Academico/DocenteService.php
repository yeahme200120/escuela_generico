<?php namespace App\Services\Academico;
use App\Models\Docente;
use App\Models\Calificacion;
use App\Models\Asistencia;

class DocenteService {
    public function obtenerEstadisticas($docenteId, $cicloId = null) {
        $docente = Docente::find($docenteId);
        if (!$docente) return null;
        
        $query = Calificacion::whereHas('materia', function($q) use ($docenteId) {
            $q->whereHas('docentes', fn($q2) => $q2->where('docente_id', $docenteId));
        });
        
        if ($cicloId) {
            $query->whereHas('periodo', fn($q) => $q->where('ciclo_escolar_id', $cicloId));
        }
        
        $calificaciones = $query->get();
        
        return [
            'docente' => $docente->nombre,
            'grupos_asignados' => $docente->grupos()->distinct()->count(),
            'materias' => $docente->materias()->count(),
            'promedio_calificaciones' => $calificaciones->avg('calificacion'),
            'tasa_aprobacion' => $calificaciones->where('calificacion', '>=', 70)->count() / max(1, $calificaciones->count()) * 100,
            'estudiantes_bajo_promedio' => $calificaciones->where('calificacion', '<', 60)->count(),
            'total_evaluaciones' => $calificaciones->count()
        ];
    }
    
    public function calcularCargaAcademica($docenteId) {
        $docente = Docente::find($docenteId);
        if (!$docente) return null;
        
        $grupos = $docente->grupos()->get();
        $materias = $docente->materias()->get();
        $horasSemanal = $materias->sum('horas_semanales');
        
        return [
            'grupos' => $grupos->count(),
            'materias' => $materias->count(),
            'estudiantes_total' => $grupos->sum('alumnos_count'),
            'horas_semanales' => $horasSemanal,
            'horas_preparacion_estimada' => $horasSemanal * 1.5
        ];
    }
    
    public function obtenerTendenciaAprobacion($docenteId, $ultimos_ciclos = 3) {
        $ciclos = \App\Models\CicloEscolar::orderBy('created_at', 'desc')->limit($ultimos_ciclos)->get();
        
        $tendencia = [];
        foreach ($ciclos as $ciclo) {
            $califs = Calificacion::whereHas('periodo', fn($q) => $q->where('ciclo_escolar_id', $ciclo->id))
                ->whereHas('materia', function($q) use ($docenteId) {
                    $q->whereHas('docentes', fn($q2) => $q2->where('docente_id', $docenteId));
                })
                ->get();
            
            $tendencia[] = [
                'ciclo' => $ciclo->nombre,
                'aprobados' => $califs->where('calificacion', '>=', 70)->count(),
                'reprobados' => $califs->where('calificacion', '<', 70)->count(),
                'promedio' => $califs->avg('calificacion')
            ];
        }
        
        return $tendencia;
    }
}
