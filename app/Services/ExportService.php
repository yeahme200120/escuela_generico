<?php namespace App\Services;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ExportService {
    public function exportarExcel($modelo, $columnas = [], $filtros = []) {
        try {
            $query = app("App\Models\\$modelo");
            
            foreach ($filtros as $campo => $valor) {
                $query = $query->where($campo, $valor);
            }
            
            $datos = $query->get($columnas ?: ['*']);
            
            $nombreArchivo = strtolower($modelo) . '_' . now()->format('Ymd_His') . '.xlsx';
            $rutaTmp = storage_path("exports/$nombreArchivo");
            
            \Log::info("Exportando $modelo a Excel: $rutaTmp");
            
            return [
                'archivo' => $nombreArchivo,
                'ruta' => $rutaTmp,
                'registros' => $datos->count()
            ];
        } catch (\Exception $e) {
            \Log::error("Error en exportación: " . $e->getMessage());
            return false;
        }
    }
    
    public function exportarPDF($modelo, $columnas = [], $titulo = '') {
        try {
            $datos = app("App\Models\\$modelo")->get($columnas ?: ['*']);
            
            $nombreArchivo = strtolower($modelo) . '_' . now()->format('Ymd_His') . '.pdf';
            $rutaTmp = storage_path("exports/$nombreArchivo");
            
            \Log::info("Exportando $modelo a PDF: $rutaTmp");
            
            return [
                'archivo' => $nombreArchivo,
                'ruta' => $rutaTmp,
                'registros' => $datos->count()
            ];
        } catch (\Exception $e) {
            \Log::error("Error en exportación PDF: " . $e->getMessage());
            return false;
        }
    }
    
    public function auditarExportacion($usuario_id, $modelo, $filtros, $formato, $cantidad) {
        DB::table('exportacion_logs')->insert([
            'usuario_id' => $usuario_id,
            'modelo' => $modelo,
            'filtros' => json_encode($filtros),
            'formato' => $formato,
            'cantidad_registros' => $cantidad,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);
    }
}
