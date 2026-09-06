<?php

namespace App\Http\Controllers;

use App\Http\Requests\Academico\CalificacionRequest;
use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\PeriodoEvaluacion;
use App\Services\Academico\CalificacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalificacionController extends Controller
{
    public function __construct(private readonly CalificacionService $calService) {}

    public function index(Request $request): View
    {
        $this->authorize('calificaciones.ver');
        $orgId     = auth()->user()->organizacion_id;
        $grupoId   = $request->grupo_id;
        $periodoId = $request->periodo_id;

        $grupos   = Grupo::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->activos()->get();
        $periodos = PeriodoEvaluacion::whereHas('cicloEscolar', fn($q) => $q->where('organizacion_id', $orgId))->orderByDesc('created_at')->get();

        $alumnos        = collect();
        $materias       = collect();
        $calificaciones = [];
        $promedios      = [];

        if ($grupoId && $periodoId) {
            $grupo    = Grupo::findOrFail($grupoId);
            $alumnos  = Alumno::where('sede_actual_id', $grupo->sede_id)->activos()->get();
            $materias = Materia::whereHas('docenteGrupoMaterias', fn($q) => $q->where('grupo_id', $grupoId))->get();

            $cals = Calificacion::where('grupo_id', $grupoId)->where('periodo_evaluacion_id', $periodoId)->get();
            foreach ($cals as $c) { $calificaciones[$c->alumno_id][$c->materia_id] = $c; }
            foreach ($alumnos as $a) {
                $vals = $cals->where('alumno_id', $a->id)->pluck('calificacion')->filter();
                $promedios[$a->id] = $vals->count() ? round($vals->avg(), 1) : null;
            }
        }

        return view('calificaciones.index', compact('grupos', 'periodos', 'alumnos', 'materias', 'calificaciones', 'promedios'));
    }

    public function create(Request $request): View
    {
        $this->authorize('calificaciones.registrar');
        $alumno  = Alumno::findOrFail($request->alumno_id);
        $materia = Materia::findOrFail($request->materia_id);
        $periodo = PeriodoEvaluacion::findOrFail($request->periodo_id);
        return view('calificaciones.create', compact('alumno', 'materia', 'periodo'));
    }

    public function store(CalificacionRequest $request): RedirectResponse
    {
        $data    = $request->validated();
        $periodo = PeriodoEvaluacion::findOrFail($data['periodo_evaluacion_id']);

        if ($periodo->cerrado && !$this->authorize('calificaciones.autorizar')) {
            return back()->with('error', 'El periodo está cerrado. Se requiere autorización especial.');
        }

        $cal = $this->calService->registrar($data, auth()->id());

        return redirect()->route('calificaciones.index', [
            'grupo_id'  => $data['grupo_id'],
            'periodo_id'=> $data['periodo_evaluacion_id'],
        ])->with('success', "Calificación registrada: {$cal->calificacion}");
    }

    public function edit(Calificacion $calificacion): View
    {
        $this->authorize('calificaciones.editar');
        return view('calificaciones.edit', compact('calificacion'));
    }

    public function update(CalificacionRequest $request, Calificacion $calificacion): RedirectResponse
    {
        $data    = $request->validated();
        $periodo = PeriodoEvaluacion::findOrFail($data['periodo_evaluacion_id']);

        if ($periodo->cerrado) {
            $this->authorize('calificaciones.autorizar');
        }

        $this->calService->registrar($data, auth()->id());
        return back()->with('success', 'Calificación actualizada.');
    }

    public function destroy(Calificacion $calificacion): RedirectResponse
    {
        $this->authorize('calificaciones.editar');
        $calificacion->delete();
        return back()->with('success', 'Calificación eliminada.');
    }
}
