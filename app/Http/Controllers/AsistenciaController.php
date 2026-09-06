<?php

namespace App\Http\Controllers;

use App\Http\Requests\Academico\AsistenciaRequest;
use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Justificante;
use App\Services\Academico\AsistenciaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsistenciaController extends Controller
{
    public function __construct(private readonly AsistenciaService $asistenciaService) {}

    public function index(Request $request): View
    {
        $this->authorize('asistencias.ver');
        $orgId  = auth()->user()->organizacion_id;
        $grupos = Grupo::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->activos()->get();

        $alumnos               = collect();
        $asistenciasExistentes = [];
        $cicloActual           = CicloEscolar::where('es_actual', true)->first();

        if ($request->grupo_id) {
            $grupo   = Grupo::find($request->grupo_id);
            $alumnos = Alumno::where('sede_actual_id', $grupo?->sede_id)->activos()->get();
            $fecha   = $request->fecha ?? now()->format('Y-m-d');
            $asistenciasExistentes = Asistencia::where('grupo_id', $request->grupo_id)
                ->where('fecha', $fecha)->get()->keyBy('alumno_id')->toArray();
        }

        return view('asistencias.index', compact('grupos', 'alumnos', 'asistenciasExistentes', 'cicloActual'));
    }

    public function store(AsistenciaRequest $request): RedirectResponse
    {
        $this->asistenciaService->registrarLista(
            grupoId:   (int) $request->grupo_id,
            materiaId: (int) ($request->materia_id ?? 0),
            docenteId: auth()->id(),
            cicloId:   (int) $request->ciclo_id,
            fecha:     $request->fecha,
            lista:     $request->lista ?? [],
            userId:    auth()->id()
        );
        return back()->with('success', 'Pase de lista guardado correctamente.');
    }

    public function edit(Asistencia $asistencia): View
    {
        $this->authorize('asistencias.editar');
        return view('asistencias.edit', compact('asistencia'));
    }

    public function update(Request $request, Asistencia $asistencia): RedirectResponse
    {
        $this->authorize('asistencias.editar');
        $request->validate(['estado' => 'required|in:presente,falta,retardo,justificada', 'observacion' => 'nullable|string|max:300']);
        $asistencia->update($request->only('estado', 'observacion'));
        return back()->with('success', 'Asistencia actualizada.');
    }

    // Justificantes
    public function justificantes(Request $request): View
    {
        $this->authorize('asistencias.ver');
        $orgId = auth()->user()->organizacion_id;
        $justificantes = Justificante::with(['alumno', 'solicitadoPor'])
            ->whereHas('alumno', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->estado, fn($q, $e) => $q->where('estado', $e))
            ->orderByDesc('created_at')->paginate(25)->withQueryString();
        return view('asistencias.justificantes', compact('justificantes'));
    }

    public function aprobarJustificante(Justificante $justificante): RedirectResponse
    {
        $this->authorize('asistencias.editar');
        $this->asistenciaService->aplicarJustificante($justificante, auth()->id());
        return back()->with('success', 'Justificante aprobado y asistencias actualizadas.');
    }
}
