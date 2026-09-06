<?php

namespace App\Http\Controllers;

use App\Http\Requests\Academico\PlanEstudioRequest;
use App\Models\Escuela;
use App\Models\Grado;
use App\Models\Materia;
use App\Models\PlanEstudio;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanEstudioController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('sedes.ver');
        $orgId  = auth()->user()->organizacion_id;
        $planes = PlanEstudio::with('escuela')
            ->whereHas('escuela', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->q, fn($q, $s) => $q->where('nombre', 'like', "%$s%"))
            ->paginate(20)->withQueryString();
        return view('planes.index', compact('planes'));
    }

    public function create(): View
    {
        $this->authorize('sedes.editar');
        $orgId    = auth()->user()->organizacion_id;
        $escuelas = Escuela::where('organizacion_id', $orgId)->activas()->get();
        $materias = Materia::whereHas('escuela', fn($q) => $q->where('organizacion_id', $orgId))->activas()->get();
        $grados   = Grado::activos()->get();
        return view('planes.create', compact('escuelas', 'materias', 'grados'));
    }

    public function store(PlanEstudioRequest $request): RedirectResponse
    {
        $data     = $request->validated();
        $materias = $data['materias'] ?? [];
        unset($data['materias']);

        $plan = PlanEstudio::create($data);

        // Asignar materias al plan con pivot
        foreach ($materias as $m) {
            $plan->materias()->attach($m['materia_id'], [
                'grado_id'    => $m['grado_id'],
                'obligatoria' => $m['obligatoria'] ?? true,
                'orden'       => $m['orden'] ?? 0,
            ]);
        }

        $this->audit->log(modulo: 'catalogos', accion: 'create', model: PlanEstudio::class, modelId: $plan->id,
            descripcion: "Plan de estudio creado: {$plan->nombre}");

        return redirect()->route('planes.show', $plan)->with('success', 'Plan de estudio creado.');
    }

    public function show(PlanEstudio $plan): View
    {
        $this->authorize('sedes.ver');
        $plan->load('escuela', 'materias');
        return view('planes.show', compact('plan'));
    }

    public function edit(PlanEstudio $plan): View
    {
        $this->authorize('sedes.editar');
        $orgId    = auth()->user()->organizacion_id;
        $materias = Materia::whereHas('escuela', fn($q) => $q->where('organizacion_id', $orgId))->activas()->get();
        $grados   = Grado::activos()->get();
        $asignadas = $plan->materias->keyBy('id');
        return view('planes.edit', compact('plan', 'materias', 'grados', 'asignadas'));
    }

    public function update(PlanEstudioRequest $request, PlanEstudio $plan): RedirectResponse
    {
        $data     = $request->validated();
        $materias = $data['materias'] ?? [];
        unset($data['materias']);

        $plan->update($data);

        $sync = [];
        foreach ($materias as $m) {
            $sync[$m['materia_id']] = ['grado_id' => $m['grado_id'], 'obligatoria' => $m['obligatoria'] ?? true, 'orden' => $m['orden'] ?? 0];
        }
        $plan->materias()->sync($sync);

        $this->audit->log(modulo: 'catalogos', accion: 'update', model: PlanEstudio::class, modelId: $plan->id);
        return redirect()->route('planes.show', $plan)->with('success', 'Plan actualizado.');
    }

    public function destroy(PlanEstudio $plan): RedirectResponse
    {
        $this->authorize('sedes.editar');
        $plan->delete();
        $this->audit->log(modulo: 'catalogos', accion: 'delete', model: PlanEstudio::class, modelId: $plan->id);
        return redirect()->route('planes.index')->with('success', 'Plan eliminado.');
    }
}
