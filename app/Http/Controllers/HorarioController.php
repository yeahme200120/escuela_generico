<?php

namespace App\Http\Controllers;

use App\Http\Requests\Academico\HorarioRequest;
use App\Models\Aula;
use App\Models\CicloEscolar;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use App\Services\Academico\HorarioConflictService;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public function __construct(
        private readonly HorarioConflictService $conflictService,
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('horarios.ver');
        $orgId = auth()->user()->organizacion_id;

        $horarios = Horario::with(['grupo', 'materia', 'docente.user', 'aula'])
            ->whereHas('grupo.sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->grupo_id, fn($q, $id) => $q->where('grupo_id', $id))
            ->when($request->ciclo_id,  fn($q, $id) => $q->where('ciclo_escolar_id', $id))
            ->orderBy('dia_semana')->orderBy('hora_inicio')
            ->paginate(40)->withQueryString();

        $grupos = Grupo::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->activos()->get();
        $ciclos = CicloEscolar::where('organizacion_id', $orgId)->orderByDesc('es_actual')->get();
        return view('horarios.index', compact('horarios', 'grupos', 'ciclos'));
    }

    public function create(): View
    {
        $this->authorize('horarios.crear');
        $orgId    = auth()->user()->organizacion_id;
        $grupos   = Grupo::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->activos()->get();
        $materias = Materia::whereHas('escuela', fn($q) => $q->where('organizacion_id', $orgId))->activas()->get();
        $docentes = Docente::with('user')->whereHas('user', fn($q) => $q->where('organizacion_id', $orgId))->activos()->get();
        $aulas    = Aula::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->activas()->get();
        $ciclos   = CicloEscolar::where('organizacion_id', $orgId)->orderByDesc('es_actual')->get();
        return view('horarios.create', compact('grupos', 'materias', 'docentes', 'aulas', 'ciclos'));
    }

    public function store(HorarioRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Verificar colisiones antes de guardar §37
        $conflictos = $this->conflictService->verificar($data);
        if ($conflictos) {
            return back()->withInput()->withErrors(['conflicto' => $conflictos]);
        }

        $horario = Horario::create($data);
        $this->audit->log(modulo: 'horarios', accion: 'create', model: Horario::class, modelId: $horario->id);
        return redirect()->route('horarios.index')->with('success', 'Horario creado sin conflictos.');
    }

    public function show(Horario $horario): View
    {
        $this->authorize('horarios.ver');
        $horario->load('grupo', 'materia', 'docente.user', 'aula');
        return view('horarios.show', compact('horario'));
    }

    public function edit(Horario $horario): View
    {
        $this->authorize('horarios.editar');
        $orgId    = auth()->user()->organizacion_id;
        $grupos   = Grupo::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->activos()->get();
        $materias = Materia::whereHas('escuela', fn($q) => $q->where('organizacion_id', $orgId))->activas()->get();
        $docentes = Docente::with('user')->whereHas('user', fn($q) => $q->where('organizacion_id', $orgId))->activos()->get();
        $aulas    = Aula::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->activas()->get();
        $ciclos   = CicloEscolar::where('organizacion_id', $orgId)->orderByDesc('es_actual')->get();
        return view('horarios.edit', compact('horario', 'grupos', 'materias', 'docentes', 'aulas', 'ciclos'));
    }

    public function update(HorarioRequest $request, Horario $horario): RedirectResponse
    {
        $data = $request->validated();
        $conflictos = $this->conflictService->verificar($data, $horario->id);
        if ($conflictos) {
            return back()->withInput()->withErrors(['conflicto' => $conflictos]);
        }
        $before = $horario->toArray();
        $horario->update($data);
        $this->audit->log(modulo: 'horarios', accion: 'update', model: Horario::class, modelId: $horario->id,
            before: $before, after: $horario->fresh()->toArray());
        return redirect()->route('horarios.show', $horario)->with('success', 'Horario actualizado.');
    }

    public function destroy(Horario $horario): RedirectResponse
    {
        $this->authorize('horarios.editar');
        $horario->delete();
        $this->audit->log(modulo: 'horarios', accion: 'delete', model: Horario::class, modelId: $horario->id);
        return redirect()->route('horarios.index')->with('success', 'Horario eliminado.');
    }

    public function publicar(Horario $horario): RedirectResponse
    {
        $this->authorize('horarios.publicar');
        $horario->update(['publicado' => true]);
        $this->audit->log(modulo: 'horarios', accion: 'publish', model: Horario::class, modelId: $horario->id);
        return back()->with('success', 'Horario publicado.');
    }
}
