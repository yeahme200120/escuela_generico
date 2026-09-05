<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\Storage;

/**
 * GeneradorReportesService — §78, §79
 * Genera reportes como CSV (sin dependencia de maatwebsite/excel).
 * Para Excel/PDF pesados delega a PythonJobService.
 */
class GeneradorReportesService
{
    public function __construct(
        private readonly AuditService $audit
    ) {}

    /**
     * Genera reporte de calificaciones como CSV y lo guarda en storage/private.
     * Registra auditoría de exportación. §79
     */
    public function generarReporteCalificaciones(array $filtros, int $userId): string
    {
        $cicloId = $filtros['ciclo_id'] ?? null;
        $grupoId = $filtros['grupo_id'] ?? null;
        $sedeId  = $filtros['sede_id']  ?? null;

        $query = Calificacion::with(['alumno', 'materia', 'periodoEvaluacion'])
            ->when($cicloId, fn($q) => $q->where('ciclo_escolar_id', $cicloId))
            ->when($grupoId, fn($q) => $q->where('grupo_id', $grupoId))
            ->when($sedeId,  fn($q) => $q->whereHas('alumno', fn($a) => $a->where('sede_actual_id', $sedeId)));

        $registros = $query->get();

        $csv = "Alumno,Materia,Periodo,Calificacion,Resultado\n";
        foreach ($registros as $cal) {
            $csv .= implode(',', [
                '"' . ($cal->alumno?->nombre_completo ?? '') . '"',
                '"' . ($cal->materia?->nombre ?? '') . '"',
                '"' . ($cal->periodoEvaluacion?->nombre ?? '') . '"',
                $cal->calificacion ?? '',
                $cal->resultado ?? '',
            ]) . "\n";
        }

        $nombre = 'reportes/calificaciones_' . now()->format('Ymd_His') . '.csv';
        Storage::disk('local')->put($nombre, $csv);

        $this->audit->log(
            modulo: 'reportes',
            accion: 'export',
            descripcion: "Exportación calificaciones: {$registros->count()} registros",
            metadata: ['archivo' => $nombre, 'filtros' => $filtros, 'registros' => $registros->count()]
        );

        return $nombre;
    }

    public function generarReporteAsistencias(array $filtros, int $userId): string
    {
        $cicloId = $filtros['ciclo_id'] ?? null;
        $grupoId = $filtros['grupo_id'] ?? null;

        $registros = Asistencia::with(['alumno', 'materia'])
            ->when($cicloId, fn($q) => $q->where('ciclo_escolar_id', $cicloId))
            ->when($grupoId, fn($q) => $q->where('grupo_id', $grupoId))
            ->get();

        $csv = "Alumno,Fecha,Estado,Materia\n";
        foreach ($registros as $a) {
            $csv .= implode(',', [
                '"' . ($a->alumno?->nombre_completo ?? '') . '"',
                $a->fecha,
                $a->estado,
                '"' . ($a->materia?->nombre ?? '') . '"',
            ]) . "\n";
        }

        $nombre = 'reportes/asistencias_' . now()->format('Ymd_His') . '.csv';
        Storage::disk('local')->put($nombre, $csv);

        $this->audit->log(
            modulo: 'reportes',
            accion: 'export',
            descripcion: "Exportación asistencias: {$registros->count()} registros",
            metadata: ['archivo' => $nombre, 'filtros' => $filtros, 'registros' => $registros->count()]
        );

        return $nombre;
    }
}
