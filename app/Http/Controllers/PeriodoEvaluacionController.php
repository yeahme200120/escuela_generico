<?php

namespace App\Http\Controllers;

use App\Http\Requests\Academico\PeriodoEvaluacionRequest;
use App\Models\CicloEscolar;
use App\Models\PeriodoEvaluacion;
use App\Services\Academico\CalificacionService;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeriodoEvaluacionController extends Controller
{
    public function __construct(
        private readonly CalificacionService $calService,
        private readonly AuditService        $audit,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('calificaciones.ver');
        $orgId = auth()->user()->organizacion_id;
        $periodos = PeriodoEvaluacion::with(['cicloEscolar'])
            ->whereHas('cicloEscolar', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->ciclo_id, fn($q, $id) => $q->where('ciclo_escolar_id', $id))
            ->orderByDesc('fecha_inicio')->paginate(20)->withQueryString();
        $ciclos = CicloEscolar::where('organizacion_id', $orgId)->orderByDesc('es_actual')->get();
        return view('periodos-evaluacion.index', compact('periodos', 'ciclos'));
    }

    public function create(): View
    {
        $this->authorize('calificaciones.cerrar');
        $orgId  = auth()->user()->organizacion_id;
        $ciclos = CicloEscolar::where('organizacion_id', $orgId)->activo()->get();
        return view('periodos-evaluacion.create', compact('ciclos'));
    }

    public function store(PeriodoEvaluacionRequest $request): RedirectResponse
    {
        $periodo = PeriodoEvaluacion::create($request->validated());
        $this->audit->log(modulo: 'calificaciones', accion: 'create', model: PeriodoEvaluacion::class, modelId: $periodo->id,
            descripcion: "Periodo creado: {$periodo->nombre}");
        return redirect()->route('periodos-evaluacion.index')->with('success', "Periodo '{$periodo->nombre}' creado.");
    }

    public function show(PeriodoEvaluacion $periodosEvaluacion): View
    {
        $this->authorize('calificaciones.ver');
        $periodosEvaluacion->load('cicloEscolar', 'calificaciones');
        return view('periodos-evaluacion.show', ['periodo' => $periodosEvaluacion]);
    }

    public function edit(PeriodoEvaluacion $periodosEvaluacion): View
    {
        $this->authorize('calificaciones.cerrar');
        $orgId  = auth()->user()->organizacion_id;
        $ciclos = CicloEscolar::where('organizacion_id', $orgId)->get();
        return view('periodos-evaluacion.edit', ['periodo' => $periodosEvaluacion, 'ciclos' => $ciclos]);
    }

    public function update(PeriodoEvaluacionRequest $request, PeriodoEvaluacion $periodosEvaluacion): RedirectResponse
    {
        if ($periodosEvaluacion->cerrado) abort(403, 'El periodo ya está cerrado.');
        $before = $periodosEvaluacion->toArray();
        $periodosEvaluacion->update($request->validated());
        $this->audit->log(modulo: 'calificaciones', accion: 'update', model: PeriodoEvaluacion::class,
            modelId: $periodosEvaluacion->id, before: $before, after: $periodosEvaluacion->fresh()->toArray());
        return redirect()->route('periodos-evaluacion.show', $periodosEvaluacion)->with('success', 'Periodo actualizado.');
    }

    public function cerrar(PeriodoEvaluacion $periodosEvaluacion): RedirectResponse
    {
        $this->authorize('calificaciones.cerrar');
        if ($periodosEvaluacion->cerrado) return back()->with('error', 'El periodo ya está cerrado.');
        $this->calService->cerrarPeriodo($periodosEvaluacion, auth()->id());
        return back()->with('success', "Periodo '{$periodosEvaluacion->nombre}' cerrado.");
    }

    public function destroy(PeriodoEvaluacion $periodosEvaluacion): RedirectResponse
    {
        $this->authorize('calificaciones.cerrar');
        if ($periodosEvaluacion->cerrado) abort(403, 'No se puede eliminar un periodo cerrado.');
        $periodosEvaluacion->delete();
        return redirect()->route('periodos-evaluacion.index')->with('success', 'Periodo eliminado.');
    }
}
