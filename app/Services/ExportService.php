<?php

namespace App\Services;

use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * ExportService — §79
 * Exportaciones auditables en CSV. Para Excel/PDF pesados usa PythonJobService.
 */
class ExportService
{
    public function __construct(private readonly AuditService $audit) {}

    /**
     * Exporta cualquier query a CSV y lo almacena en storage.
     * Registra auditoría completa: quién, qué, filtros, nro. registros, archivo, Request ID.
     */
    public function exportarCSV(string $modulo, \Illuminate\Database\Eloquent\Builder $query, array $columnas, array $filtros = []): string
    {
        $registros = $query->get();
        $nombre    = "exportaciones/{$modulo}_" . now()->format('Ymd_His') . '.csv';

        $csv = implode(',', array_keys($columnas)) . "\n";
        foreach ($registros as $row) {
            $linea = [];
            foreach ($columnas as $campo => $etiqueta) {
                $val = data_get($row, $campo, '');
                $linea[] = '"' . str_replace('"', '""', $val) . '"';
            }
            $csv .= implode(',', $linea) . "\n";
        }

        Storage::disk('local')->put($nombre, $csv);

        $this->audit->log(
            modulo:      $modulo,
            accion:      'export',
            descripcion: "Exportación CSV: {$registros->count()} registros",
            metadata: [
                'archivo'   => $nombre,
                'filtros'   => $filtros,
                'registros' => $registros->count(),
                'columnas'  => array_keys($columnas),
            ]
        );

        return $nombre;
    }
}
