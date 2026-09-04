<?php
namespace App\Services\Python;

use App\Models\PythonJob;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Str;

/**
 * PythonJobService — §76–§78, §109
 * Despacha jobs al servicio Python via HTTP (FastAPI) o Queue Redis.
 * El navegador NUNCA se comunica directamente con Python. §3
 */
class PythonJobService
{
    private string $pythonUrl;
    private string $secret;
    private int    $timeout;

    public function __construct(private readonly AuditService $audit)
    {
        $this->pythonUrl = config('python.url', 'http://localhost:8001');
        $this->secret    = config('python.secret', '');
        $this->timeout   = config('python.timeout', 300);
    }

    /**
     * Crea un PythonJob y lo encola.
     * @param string $tipo calcular_indicadores|calcular_riesgo|importacion|generar_horario|reporte
     */
    public function despachar(string $tipo, array $payload, int $orgId, int $userId): PythonJob
    {
        $job = PythonJob::create([
            'job_id'          => 'JOB-' . strtoupper(Str::ulid()),
            'organizacion_id' => $orgId,
            'usuario_id'      => $userId,
            'tipo'            => $tipo,
            'payload'         => $payload,
            'estado'          => 'pendiente',
        ]);

        // Encolar job de Laravel que llamará al servicio Python
        \App\Jobs\DispatchPythonJob::dispatch($job->id);

        $this->audit->log(modulo:'python',accion:'dispatch',descripcion:"Job {$tipo} #{$job->job_id}",model:PythonJob::class,modelId:$job->id);

        return $job;
    }

    /**
     * Llamada HTTP al servicio FastAPI.
     * Usada internamente por DispatchPythonJob.
     */
    public function ejecutar(PythonJob $job): array
    {
        $job->update(['estado'=>'procesando','iniciado_at'=>now()]);

        try {
            $client   = new \Illuminate\Http\Client\PendingRequest();
            $response = \Illuminate\Support\Facades\Http::timeout($this->timeout)
                ->withHeaders(['X-Python-Secret' => $this->secret, 'X-Job-ID' => $job->job_id])
                ->post("{$this->pythonUrl}/jobs/{$job->tipo}", $job->payload ?? []);

            if ($response->successful()) {
                $resultado = $response->json();
                $job->update(['estado'=>'completado','resultado'=>$resultado,'completado_at'=>now(),'progreso'=>100]);
                return $resultado;
            }

            throw new \RuntimeException("Python service error: HTTP {$response->status()}");

        } catch (\Throwable $e) {
            $job->update(['estado'=>'fallido','error'=>$e->getMessage()]);
            $this->audit->logError('python', $job->tipo, $e, ['job_id'=>$job->job_id]);
            throw $e;
        }
    }

    public function obtenerEstado(string $jobId): ?PythonJob
    {
        return PythonJob::where('job_id', $jobId)->first();
    }
}
