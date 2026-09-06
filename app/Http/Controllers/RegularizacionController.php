<?php

namespace App\Http\Controllers;

use App\Http\Requests\Academico\RegularizacionRequest;
use App\Models\Alumno;
use App\Models\CicloEscolar;
use App\Models\Materia;
use App\Models\Regularizacion;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegularizacionController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('calificaciones.ver');
        $orgId = auth()->user()->organizacion_id;

        $regularizaciones = Regularizacion::with(['alumno', 'materia', 'cicloEscolar'])
            ->whereHas('alumno', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->resultado,  fn($q, $r) => $q->where('resultado', $r))
            ->when($request->ciclo_id,   fn($q, $id) => $q->where('ciclo_escolar_id', $id))
            ->when($request->alumno_id,  fn($q, $id) => $q->where('alumno_id', $id))
            ->orderByDesc('created_at')->paginate(25)->withQueryString();

        return view('regularizaciones.index', compact('regularizaciones'));
    }

    public function create(Request $request): View
    {
        $this->authorize('calificaciones.registrar');
        $orgId   = auth()->user()->organizacion_id;
        $alumnos = Alumno::where('organizacion_id', $orgId)->activos()->orderBy('apellido_paterno')->get();
        $materias = Materia::whereHas('escuela', fn($q) => $q->where('organizacion_id', $orgId))->activas()->get();
        $ciclos   = CicloEscolar::where('organizacion_id', $orgId)->orderByDesc('es_actual')->get();
        $alumnoSeleccionado = $request->alumno_id ? Alumno::find($request->alumno_id) : null;
        return view('regularizaciones.create', compact('alumnos', 'materias', 'ciclos', 'alumnoSeleccionado'));
    }

    public function store(RegularizacionRequest $request): RedirectResponse
    {
        $reg = Regularizacion::create($request->validated() + ['usuario_id' => auth()->id()]);
        $this->audit->log(modulo: 'calificaciones', accion: 'regularizacion', model: Regularizacion::class, modelId: $reg->id,
            descripcion: "Regularización alumno#{$reg->alumno_id} materia#{$reg->materia_id}");
        return redirect()->route('regularizaciones.index')->with('success', 'Regularización registrada.');
    }

    public function show(Regularizacion $regularizacion): View
    {
        $regularizacion->load('alumno', 'materia', 'cicloEscolar', 'usuario');
        return view('regularizaciones.show', compact('regularizacion'));
    }

    public function edit(Regularizacion $regularizacion): View
    {
        $this->authorize('calificaciones.editar');
        return view('regularizaciones.edit', compact('regularizacion'));
    }

    public function update(RegularizacionRequest $request, Regularizacion $regularizacion): RedirectResponse
    {
        $before = $regularizacion->toArray();
        $regularizacion->update($request->validated());
        $this->audit->log(modulo: 'calificaciones', accion: 'update', model: Regularizacion::class, modelId: $regularizacion->id,
            before: $before, after: $regularizacion->fresh()->toArray());
        return redirect()->route('regularizaciones.show', $regularizacion)->with('success', 'Regularización actualizada.');
    }

    public function destroy(Regularizacion $regularizacion): RedirectResponse
    {
        $this->authorize('calificaciones.editar');
        $regularizacion->delete();
        return redirect()->route('regularizaciones.index')->with('success', 'Regularización eliminada.');
    }
}
