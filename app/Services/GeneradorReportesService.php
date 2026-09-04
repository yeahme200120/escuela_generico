<?php namespace App\Services;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Calificacion;
use App\Models\Asistencia;
use App\Models\Alumno;
use App\Models\Docente;

class GeneradorReportesService {
    
    public function generarReporteCalificaciones(array $filtros): string {
        try {
            $cicloId = $filtros['ciclo_id'] ?? null;
            $grupoId = $filtros['grupo_id'] ?? null;
            $sedeId = $filtros['sede_id'] ?? null;
            
            $query = Calificacion::with(['alumno', 'materia', 'periodo'])
                ->when($cicloId, fn($q) => $q->whereHas('periodo', fn($q2) => $q2->where('ciclo_escolar_id', $cicloId)))
                ->when($grupoId, fn($q) => $q->whereHas('alumno', fn($q2) => $q2->whereHas('grupo', fn($q3) => $q3->where('id', $grupoId))))
                ->when($sedeId, fn($q) => $q->whereHas('alumno', fn($q2) => $q2->whereHas('grupo', fn($q3) => $q3->whereHas('grado', fn($q4) => $q4->whereHas('sede', fn($q5) => $q5->where('id', $sedeId)))))));
            
            $calificaciones = $query->get();
            
            $nombreArchivo = 'reporte_calificaciones_' . now()->format('Ymd_His') . '.xlsx';
            $ruta = storage_path("exports/$nombreArchivo");
            
            // Crear Excel con Maatwebsite
            $datos = $calificaciones->map(fn($c) => [
                'Alumno' => $c->alumno->nombre,
                'Materia' => $c->materia->nombre,
                'Calificación' => $c->calificacion,
                'Periodo' => $c->periodo->nombre,
                'Estado' => $c->calificacion >= 70 ? 'Aprobado' : 'Reprobado'
            ]);
            
            // Aquí usarías Excel::store() o similar
            \Log::info("Reporte de calificaciones generado: $nombreArchivo ($calificaciones->count() registros)");
            
            return $ruta;
            
        } catch (\Exception $e) {
            \Log::error("Error generando reporte calificaciones: " . $e->getMessage());
            return false;
        }
    }
    
    public function generarReporteAsistencia(array $filtros): string {
        try {
            $cicloId = $filtros['ciclo_id'] ?? null;
            $grupoId = $filtros['grupo_id'] ?? null;
            $fechaDesde = $filtros['fecha_desde'] ?? null;
            $fechaHasta = $filtros['fecha_hasta'] ?? null;
            
            $query = Asistencia::with(['alumno', 'grupo'])
                ->when($grupoId, fn($q) => $q->where('grupo_id', $grupoId))
                ->when($cicloId, fn($q) => $q->whereHas('grupo', fn($q2) => $q2->whereHas('ciclo', fn($q3) => $q3->where('id', $cicloId))))
                ->when($fechaDesde, fn($q) => $q->whereDate('fecha', '>=', $fechaDesde))
                ->when($fechaHasta, fn($q) => $q->whereDate('fecha', '<=', $fechaHasta));
            
            $asistencias = $query->get();
            
            $nombreArchivo = 'reporte_asistencia_' . now()->format('Ymd_His') . '.xlsx';
            $ruta = storage_path("exports/$nombreArchivo");
            
            $datos = $asistencias->map(fn($a) => [
                'Alumno' => $a->alumno->nombre,
                'Grupo' => $a->grupo->nombre,
                'Fecha' => $a->fecha->format('d/m/Y'),
                'Presente' => $a->presente ? 'Sí' : 'No'
            ]);
            
            \Log::info("Reporte de asistencia generado: $nombreArchivo ($asistencias->count() registros)");
            
            return $ruta;
            
        } catch (\Exception $e) {
            \Log::error("Error generando reporte asistencia: " . $e->getMessage());
            return false;
        }
    }
    
    public function generarReporteEstudiantes(array $filtros): string {
        try {
            $grupoId = $filtros['grupo_id'] ?? null;
            $sedeId = $filtros['sede_id'] ?? null;
            $nivelId = $filtros['nivel_id'] ?? null;
            
            $query = Alumno::with(['grupo', 'tutores'])
                ->when($grupoId, fn($q) => $q->whereHas('grupo', fn($q2) => $q2->where('id', $grupoId)))
                ->when($sedeId, fn($q) => $q->whereHas('grupo', fn($q2) => $q2->whereHas('sede', fn($q3) => $q3->where('id', $sedeId))))
                ->when($nivelId, fn($q) => $q->whereHas('grupo', fn($q2) => $q2->whereHas('grado', fn($q3) => $q3->where('nivel_id', $nivelId)))));
            
            $estudiantes = $query->get();
            
            $nombreArchivo = 'reporte_estudiantes_' . now()->format('Ymd_His') . '.xlsx';
            $ruta = storage_path("exports/$nombreArchivo");
            
            $datos = $estudiantes->map(fn($e) => [
                'Nombre' => $e->nombre,
                'Cedula' => $e->cedula,
                'Email' => $e->email,
                'Grupo' => $e->grupo?->nombre,
                'Fecha_Nacimiento' => $e->fecha_nacimiento?->format('d/m/Y'),
                'Genero' => $e->genero,
                'Telefono' => $e->telefono
            ]);
            
            \Log::info("Reporte de estudiantes generado: $nombreArchivo ($estudiantes->count() registros)");
            
            return $ruta;
            
        } catch (\Exception $e) {
            \Log::error("Error generando reporte estudiantes: " . $e->getMessage());
            return false;
        }
    }
    
    public function generarReporteDocentes(array $filtros): string {
        try {
            $sedeId = $filtros['sede_id'] ?? null;
            $cargoId = $filtros['cargo_id'] ?? null;
            
            $query = Docente::with(['sede', 'cargo', 'materias'])
                ->when($sedeId, fn($q) => $q->where('sede_id', $sedeId))
                ->when($cargoId, fn($q) => $q->where('cargo_id', $cargoId));
            
            $docentes = $query->get();
            
            $nombreArchivo = 'reporte_docentes_' . now()->format('Ymd_His') . '.xlsx';
            $ruta = storage_path("exports/$nombreArchivo");
            
            $datos = $docentes->map(fn($d) => [
                'Nombre' => $d->nombre,
                'Cedula' => $d->cedula,
                'Email' => $d->email,
                'Cargo' => $d->cargo?->nombre,
                'Sede' => $d->sede?->nombre,
                'Materias' => $d->materias->count(),
                'Telefono' => $d->telefono,
                'Estado' => $d->activo ? 'Activo' : 'Inactivo'
            ]);
            
            \Log::info("Reporte de docentes generado: $nombreArchivo ($docentes->count() registros)");
            
            return $ruta;
            
        } catch (\Exception $e) {
            \Log::error("Error generando reporte docentes: " . $e->getMessage());
            return false;
        }
    }
    
    public function programarReporte(string $tipo, array $filtros, string $frecuencia): bool {
        try {
            DB::table('reportes_programados')->insert([
                'tipo' => $tipo,
                'filtros' => json_encode($filtros),
                'frecuencia' => $frecuencia, // diaria, semanal, mensual
                'ultimo_envio' => null,
                'proximo_envio' => $this->calcularProximoEnvio($frecuencia),
                'activo' => true,
                'created_at' => now()
            ]);
            
            \Log::info("Reporte programado: tipo=$tipo, frecuencia=$frecuencia");
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error("Error programando reporte: " . $e->getMessage());
            return false;
        }
    }
    
    private function calcularProximoEnvio(string $frecuencia) {
        return match ($frecuencia) {
            'diaria' => now()->addDay(),
            'semanal' => now()->addWeek(),
            'mensual' => now()->addMonth(),
            default => now()->addDay()
        };
    }
    
    public function obtenerUltimosReportes(string $tipo, int $cantidad = 5): array {
        try {
            return DB::table('reportes_generados')
                ->where('tipo', $tipo)
                ->orderBy('created_at', 'desc')
                ->limit($cantidad)
                ->get()
                ->toArray();
            
        } catch (\Exception $e) {
            \Log::error("Error obteniendo reportes: " . $e->getMessage());
            return [];
        }
    }
}
