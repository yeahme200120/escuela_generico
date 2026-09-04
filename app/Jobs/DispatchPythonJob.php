<?php namespace App\Jobs;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchPythonJob implements ShouldQueue {
    use Queueable;
    
    public int $tries = 3;
    public int $timeout = 300;
    
    private string $workerType;
    private array $datos;
    private ?int $usuarioId;
    
    public function __construct(string $workerType, array $datos, ?int $usuarioId = null) {
        $this->workerType = $workerType;
        $this->datos = $datos;
        $this->usuarioId = $usuarioId;
    }
    
    public function handle() {
        try {
            Log::info("Despachando trabajo Python: $this->workerType", $this->datos);
            
            // Preparar payload
            $payload = [
                'tipo' => $this->workerType,
                'datos' => $this->datos,
                'usuario_id' => $this->usuarioId,
                'timestamp' => now()->toIso8601String()
            ];
            
            // Obtener URL de FastAPI
            $pythonUrl = config('services.python.url', 'http://localhost:8001');
            $endpoint = "$pythonUrl/workers/" . str_replace('_', '-', $this->workerType);
            
            // Realizar llamada HTTP
            $response = Http::timeout(300)
                ->withHeader('X-Python-Secret', config('services.python.secret'))
                ->post($endpoint, $payload);
            
            if ($response->failed()) {
                Log::warning("Python worker respondió con error: " . $response->status());
                throw new \Exception("Worker error: " . $response->body());
            }
            
            $result = $response->json();
            
            Log::info("Trabajo Python completado: $this->workerType", $result);
            
            // Guardar resultado en logs
            $this->registrarResultado($result);
            
        } catch (\Exception $e) {
            Log::error("Error en DispatchPythonJob: " . $e->getMessage());
            
            // Si tenemos intentos restantes, reintentar
            if ($this->attempts() < $this->tries) {
                $this->release(delay: 60 * $this->attempts()); // Backoff exponencial
            } else {
                Log::error("Trabajo Python fallido después de {$this->tries} intentos: $this->workerType");
                $this->registrarError($e);
            }
        }
    }
    
    private function registrarResultado(array $resultado): void {
        try {
            \DB::table('python_job_logs')->insert([
                'worker_type' => $this->workerType,
                'usuario_id' => $this->usuarioId,
                'estado' => 'completado',
                'entrada' => json_encode($this->datos),
                'salida' => json_encode($resultado),
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error("Error registrando resultado: " . $e->getMessage());
        }
    }
    
    private function registrarError(\Exception $e): void {
        try {
            \DB::table('python_job_logs')->insert([
                'worker_type' => $this->workerType,
                'usuario_id' => $this->usuarioId,
                'estado' => 'error',
                'entrada' => json_encode($this->datos),
                'error' => $e->getMessage(),
                'created_at' => now()
            ]);
        } catch (\Exception $ex) {
            Log::error("Error registrando error: " . $ex->getMessage());
        }
    }
    
    public function failed(\Throwable $exception): void {
        Log::error("Job fallido permanentemente", [
            'worker' => $this->workerType,
            'error' => $exception->getMessage()
        ]);
    }
}
