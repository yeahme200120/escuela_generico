<?php
namespace App\Jobs;

use App\Models\PythonJob;
use App\Services\Python\PythonJobService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * DispatchPythonJob — §38, §76–§78
 * Ejecuta el job Python de forma asíncrona.
 * El resultado queda en python_jobs.resultado para polling desde el frontend.
 */
class DispatchPythonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 600;

    public function __construct(private readonly int $pythonJobId) {}

    public function handle(PythonJobService $pythonService): void
    {
        $job = PythonJob::findOrFail($this->pythonJobId);
        $pythonService->ejecutar($job);
    }

    public function failed(\Throwable $exception): void
    {
        PythonJob::where('id', $this->pythonJobId)->update([
            'estado' => 'fallido',
            'error'  => $exception->getMessage(),
        ]);
    }
}
