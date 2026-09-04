<?php namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\DispatchPythonJob;

class PythonJobService {
    
    private string $baseUrl;
    private string $secret;
    private int $timeout = 300;
    
    public function __construct() {
        $this->baseUrl = config('services.python.url', 'http://localhost:8001');
        $this->secret = config('services.python.secret', 'dev-secret-key');
    }
    
    /**
     * Despachar trabajo de forma asincrónica (queue)
     */
    public function despacharAsync(string $workerType, array $datos, ?int $usuarioId = null): void {
        Log::info("Despachando job async: $workerType");
        dispatch(new DispatchPythonJob($workerType, $datos, $usuarioId));
    }
    
    /**
     * Ejecutar trabajo de forma sincrónica (esperar respuesta)
     */
    public function despacharSync(string $workerType, array $datos): array {
        try {
            Log::info("Despachando job sync: $workerType", $datos);
            
            $endpoint = "$this->baseUrl/workers/" . str_replace('_', '-', $workerType);
            
            $response = Http::timeout($this->timeout)
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('X-Python-Secret', $this->secret)
                ->post($endpoint, [
                    'tipo' => $workerType,
                    'datos' => $datos
                ]);
            
            if ($response->failed()) {
                Log::error("Python worker error: {$response->status()}", ['body' => $response->body()]);
                return ['error' => "Worker error: {$response->status()}"];
            }
            
            return $response->json();
            
        } catch (\Exception $e) {
            Log::error("Error en despacho sync: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    /**
     * Verificar estado del servicio Python
     */
    public function verificarSalud(): bool {
        try {
            $response = Http::timeout(5)
                ->withHeader('X-Python-Secret', $this->secret)
                ->get("$this->baseUrl/health");
            
            if ($response->ok()) {
                $data = $response->json();
                Log::info("Python health check OK", $data);
                return true;
            }
            
            return false;
            
        } catch (\Exception $e) {
            Log::warning("Python health check failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener lista de workers disponibles
     */
    public function obtenerWorkersDisponibles(): array {
        try {
            $response = Http::timeout(10)
                ->withHeader('X-Python-Secret', $this->secret)
                ->get("$this->baseUrl/workers/status");
            
            if ($response->ok()) {
                return $response->json()['workers'] ?? [];
            }
            
            return [];
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo workers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calcular indicadores académicos
     */
    public function calcularIndicadores(int $sedeId, int $cicloId): array {
        return $this->despacharSync('calcular_indicadores', [
            'sede_id' => $sedeId,
            'ciclo_id' => $cicloId,
            'fecha_inicio' => now()->startOfYear()->toDateString(),
            'fecha_fin' => now()->endOfYear()->toDateString()
        ]);
    }
    
    /**
     * Calcular riesgo académico de grupo
     */
    public function calcularRiesgo(int $grupoId, int $cicloId): array {
        return $this->despacharSync('calcular_riesgo', [
            'grupo_id' => $grupoId,
            'ciclo_id' => $cicloId
        ]);
    }
    
    /**
     * Generar reportes masivos
     */
    public function generarReportes(string $tipoReporte, array $filtros = [], string $formato = 'excel'): array {
        return $this->despacharSync('generar_reportes', [
            'tipo' => $tipoReporte,
            'filtros' => $filtros,
            'formato' => $formato,
            'fecha_inicio' => $filtros['fecha_desde'] ?? now()->startOfMonth()->toDateString(),
            'fecha_fin' => $filtros['fecha_hasta'] ?? now()->toDateString()
        ]);
    }
    
    /**
     * Procesar importación masiva
     */
    public function procesarImportacion(string $rutaArchivo, string $tipoImportacion, array $mapping = []): array {
        return $this->despacharSync('procesar_importaciones', [
            'archivo_ruta' => $rutaArchivo,
            'tipo_datos' => $tipoImportacion,
            'mapping' => $mapping,
            'validar_duplicados' => true
        ]);
    }
    
    /**
     * Despachar trabajo asincrónico de importación
     */
    public function procesarImportacionAsync(string $rutaArchivo, string $tipoImportacion, ?int $usuarioId = null): void {
        $this->despacharAsync('procesar_importaciones', [
            'archivo_ruta' => $rutaArchivo,
            'tipo_datos' => $tipoImportacion,
            'validar_duplicados' => true
        ], $usuarioId);
    }
    
    /**
     * Despachar cálculo de indicadores asincrónico
     */
    public function calcularIndicadoresAsync(int $sedeId, int $cicloId, ?int $usuarioId = null): void {
        $this->despacharAsync('calcular_indicadores', [
            'sede_id' => $sedeId,
            'ciclo_id' => $cicloId
        ], $usuarioId);
    }
    
    /**
     * Obtener logs de trabajos Python
     */
    public function obtenerLogs(string $workerType = null, int $limite = 50): array {
        try {
            $query = \DB::table('python_job_logs')
                ->orderBy('created_at', 'desc')
                ->limit($limite);
            
            if ($workerType) {
                $query->where('worker_type', $workerType);
            }
            
            return $query->get()->toArray();
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo logs: " . $e->getMessage());
            return [];
        }
    }
}
