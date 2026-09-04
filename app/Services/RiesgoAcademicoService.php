<?php namespace App\Services;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Asistencia;
use Illuminate\Support\Facades\DB;

class RiesgoAcademicoService {
    
    const BAJO = 'bajo';       // score < 40
    const MEDIO = 'medio';     // score 40-60
    const ALTO = 'alto';       // score 60-80
    const CRITICO = 'crítico'; // score > 80
    
    public function evaluarRiesgo(int $alumnoId, int $cicloId): array {
        try {
            $alumno = Alumno::find($alumnoId);
            if (!$alumno) {
                return ['error' => 'Alumno no encontrado'];
            }
            
            $score = $this->calcularScore($alumnoId, $cicloId);
            $nivel = $this->determinarNivel($score);
            $factores = $this->obtenerFactoresRiesgo($alumnoId, $cicloId);
            
            $evaluacion = [
                'alumno_id' => $alumnoId,
                'ciclo_id' => $cicloId,
                'alumno_nombre' => $alumno->nombre,
                'score' => $score,
                'nivel' => $nivel,
                'factores' => $factores,
                'fecha_evaluacion' => now()->toDateTimeString(),
                'recomendaciones' => $this->generarRecomendaciones($nivel, $factores)
            ];
            
            // Generar alerta si es necesario
            if ($nivel === self::ALTO || $nivel === self::CRITICO) {
                $this->generarAlerta($alumnoId, $nivel);
            }
            
            return $evaluacion;
            
        } catch (\Exception $e) {
            \Log::error("Error evaluando riesgo: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    public function calcularScore(int $alumnoId, ?int $cicloId = null): float {
        $score = 0;
        
        // 1. Calificaciones bajas (max 35 puntos)
        $calificaciones = Calificacion::where('alumno_id', $alumnoId)
            ->when($cicloId, fn($q) => $q->whereHas('periodo', fn($q2) => $q2->where('ciclo_escolar_id', $cicloId)))
            ->get();
        
        if ($calificaciones->count() > 0) {
            $promedio = $calificaciones->avg('calificacion');
            if ($promedio < 60) {
                $score += 35;
            } elseif ($promedio < 70) {
                $score += 20;
            } elseif ($promedio < 80) {
                $score += 5;
            }
        }
        
        // 2. Materias reprobadas (max 25 puntos)
        $reprobadas = $calificaciones->where('calificacion', '<', 70)->count();
        $score += min($reprobadas * 8, 25);
        
        // 3. Inasistencias (max 20 puntos)
        $asistencias = Asistencia::where('alumno_id', $alumnoId)
            ->when($cicloId, fn($q) => $q->whereHas('grupo', fn($q2) => $q2->whereHas('ciclo', fn($q3) => $q3->where('id', $cicloId))))
            ->where('presente', false)
            ->count();
        
        $totalAsistencias = Asistencia::where('alumno_id', $alumnoId)->count();
        $porcentajeAsistencia = $totalAsistencias > 0 ? ($asistencias / $totalAsistencias) * 100 : 0;
        
        if ($porcentajeAsistencia > 30) {
            $score += 20;
        } elseif ($porcentajeAsistencia > 15) {
            $score += 12;
        } elseif ($porcentajeAsistencia > 5) {
            $score += 5;
        }
        
        // 4. Comportamiento / Disciplina (max 15 puntos) - suponiendo tabla
        $disciplina = DB::table('disciplina_registros')
            ->where('alumno_id', $alumnoId)
            ->when($cicloId, fn($q) => $q->where('ciclo_id', $cicloId))
            ->count();
        
        $score += min($disciplina * 3, 15);
        
        // 5. Atrasos en pagos (max 5 puntos)
        $pagos_vencidos = DB::table('parcialidades')
            ->where('alumno_id', $alumnoId)
            ->where('estado', 'vencido')
            ->count();
        
        $score += min($pagos_vencidos * 2.5, 5);
        
        return round(min($score, 100), 2);
    }
    
    private function determinarNivel(float $score): string {
        return match (true) {
            $score < 40 => self::BAJO,
            $score < 60 => self::MEDIO,
            $score < 80 => self::ALTO,
            default => self::CRITICO
        };
    }
    
    public function obtenerFactoresRiesgo(int $alumnoId, ?int $cicloId = null): array {
        $factores = [];
        
        // Verificar cada factor
        $calificaciones = Calificacion::where('alumno_id', $alumnoId)
            ->when($cicloId, fn($q) => $q->whereHas('periodo', fn($q2) => $q2->where('ciclo_escolar_id', $cicloId)))
            ->get();
        
        if ($calificaciones->count() > 0) {
            $promedio = $calificaciones->avg('calificacion');
            
            if ($promedio < 60) {
                $factores[] = [
                    'tipo' => 'calificaciones_muy_bajas',
                    'descripcion' => "Promedio $promedio < 60",
                    'severidad' => 'crítica'
                ];
            }
            
            $reprobadas = $calificaciones->where('calificacion', '<', 70)->count();
            if ($reprobadas > 2) {
                $factores[] = [
                    'tipo' => 'multiples_reprobadas',
                    'descripcion' => "$reprobadas materias reprobadas",
                    'severidad' => 'alta'
                ];
            }
        }
        
        // Inasistencias
        $inasistencias = Asistencia::where('alumno_id', $alumnoId)
            ->where('presente', false)->count();
        
        if ($inasistencias > 10) {
            $factores[] = [
                'tipo' => 'inasistencias_frecuentes',
                'descripcion' => "$inasistencias faltas",
                'severidad' => 'alta'
            ];
        }
        
        // Disciplina
        $infracciones = DB::table('disciplina_registros')
            ->where('alumno_id', $alumnoId)
            ->count();
        
        if ($infracciones > 3) {
            $factores[] = [
                'tipo' => 'problemas_disciplina',
                'descripcion' => "$infracciones incidentes disciplinarios",
                'severidad' => 'media'
            ];
        }
        
        return $factores;
    }
    
    public function generarAlerta(int $alumnoId, string $nivel): bool {
        try {
            DB::table('alertas_riesgo')->insertOrIgnore([
                'alumno_id' => $alumnoId,
                'nivel' => $nivel,
                'estado' => 'activa',
                'fecha_creacion' => now(),
                'fecha_visualizacion' => null
            ]);
            
            \Log::warning("Alerta de riesgo generada para alumno $alumnoId (nivel: $nivel)");
            return true;
            
        } catch (\Exception $e) {
            \Log::error("Error generando alerta: " . $e->getMessage());
            return false;
        }
    }
    
    public function obtenerEstudiantesEnRiesgo(int $grupoId): array {
        try {
            $alumnos = Alumno::whereHas('grupo', fn($q) => $q->where('id', $grupoId))->get();
            
            $enRiesgo = [];
            foreach ($alumnos as $alumno) {
                $score = $this->calcularScore($alumno->id);
                $nivel = $this->determinarNivel($score);
                
                if ($nivel !== self::BAJO) {
                    $enRiesgo[] = [
                        'alumno_id' => $alumno->id,
                        'nombre' => $alumno->nombre,
                        'score' => $score,
                        'nivel' => $nivel
                    ];
                }
            }
            
            return $enRiesgo;
            
        } catch (\Exception $e) {
            \Log::error("Error obteniendo estudiantes en riesgo: " . $e->getMessage());
            return [];
        }
    }
    
    private function generarRecomendaciones(string $nivel, array $factores): array {
        $recomendaciones = [];
        
        match ($nivel) {
            self::BAJO => $recomendaciones[] = 'Mantener el desempeño actual',
            self::MEDIO => [
                $recomendaciones[] = 'Sesión de seguimiento con tutor',
                $recomendaciones[] = 'Revisar métodos de estudio'
            ],
            self::ALTO => [
                $recomendaciones[] = 'Tutorías intensivas',
                $recomendaciones[] = 'Reunión con padres',
                $recomendaciones[] = 'Plan de mejora académica'
            ],
            self::CRITICO => [
                $recomendaciones[] = 'Intervención inmediata del equipo psicosocial',
                $recomendaciones[] = 'Evaluación psicológica',
                $recomendaciones[] = 'Posible cambio de grupo',
                $recomendaciones[] = 'Seguimiento semanal'
            ]
        };
        
        foreach ($factores as $factor) {
            if ($factor['tipo'] === 'inasistencias_frecuentes') {
                $recomendaciones[] = 'Investigar causas de ausencias - contacto familiar urgente';
            } elseif ($factor['tipo'] === 'problemas_disciplina') {
                $recomendaciones[] = 'Derivación a orientación escolar';
            }
        }
        
        return $recomendaciones;
    }
}
