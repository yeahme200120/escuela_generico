<?php

namespace App\Http\Controllers;

use App\Models\Reporte;
use App\Services\GeneradorReportesService;
use App\Services\Python\PythonJobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function __construct(
        private readonly GeneradorReportesService $generador,
        private readonly PythonJobService $pythonService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('reportes.ver');
        $orgId = auth()->user()->organizacion_id;
        $reportes = Reporte::where('organizacion_id', $orgId)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();
        return view('reportes.index', compact('reportes'));
    }

    public function create(): View
    {
        $this->authorize('reportes.ver');
        return view('reportes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('reportes.exportar');
        $data = $request->validate([
            'tipo'        => 'required|in:calificaciones,asistencias,alumnos,docentes,indicadores',
            'ciclo_id'    => 'nullable|exists:ciclos_escolares,id',
            'grupo_id'    => 'nullable|exists:grupos,id',
            'sede_id'     => 'nullable|exists:sedes,id',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date',
        ]);

        $orgId  = auth()->user()->organizacion_id;
        $userId = auth()->id();

        // Reportes pequeños: generar CSV directo
        if (in_array($data['tipo'], ['calificaciones', 'asistencias'])) {
            if ($data['tipo'] === 'calificaciones') {
                $archivo = $this->generador->generarReporteCalificaciones($data, $userId);
            } else {
                $archivo = $this->generador->generarReporteAsistencias($data, $userId);
            }
            return redirect()->route('reportes.index')
                ->with('success', "Reporte generado: {$archivo}");
        }

        // Reportes grandes: encolar a Python §78
        $job = $this->pythonService->despachar(
            tipo:    'reporte',
            payload: $data,
            orgId:   $orgId,
            userId:  $userId,
        );

        return redirect()->route('reportes.index')
            ->with('success', "Reporte encolado. Job ID: {$job->job_id}");
    }

    public function show(int $id): View
    {
        $this->authorize('reportes.ver');
        $reporte = Reporte::where('organizacion_id', auth()->user()->organizacion_id)
            ->findOrFail($id);
        return view('reportes.show', compact('reporte'));
    }

    public function descargar(int $id): mixed
    {
        $this->authorize('reportes.exportar');
        $reporte = Reporte::where('organizacion_id', auth()->user()->organizacion_id)
            ->findOrFail($id);

        if (!$reporte->tieneArchivo()) {
            return back()->with('error', 'El archivo no está disponible aún.');
        }

        return Storage::disk('local')->download($reporte->archivo_resultado);
    }
}
