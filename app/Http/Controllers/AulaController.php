<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Edificio;
use App\Models\Sede;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AulaController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('sedes.ver');
        $orgId = auth()->user()->organizacion_id;
        $aulas = Aula::with(['sede', 'edificio'])
            ->whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->sede_id, fn($q, $id) => $q->where('sede_id', $id))
            ->when($request->tipo,    fn($q, $t)  => $q->where('tipo', $t))
            ->paginate(25)->withQueryString();
        $sedes = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        return view('aulas.index', compact('aulas', 'sedes'));
    }

    public function create(): View
    {
        $this->authorize('sedes.editar');
        $orgId     = auth()->user()->organizacion_id;
        $sedes     = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        $edificios = Edificio::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->get();
        return view('aulas.create', compact('sedes', 'edificios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('sedes.editar');
        $data = $request->validate([
            'sede_id'         => 'required|exists:sedes,id',
            'edificio_id'     => 'nullable|exists:edificios,id',
            'nombre'          => 'required|string|max:100',
            'clave'           => 'nullable|string|max:30',
            'tipo'            => 'required|in:salon,laboratorio,taller,sala_computo,sala_usos_multiples,auditorio',
            'capacidad'       => 'required|integer|min:1|max:500',
            'piso'            => 'nullable|integer',
            'tiene_proyector' => 'boolean',
            'tiene_ac'        => 'boolean',
        ]);
        $aula = Aula::create($data);
        $this->audit->log(modulo: 'catalogos', accion: 'create', model: Aula::class, modelId: $aula->id,
            descripcion: "Aula: {$aula->nombre}");
        return redirect()->route('aulas.index')->with('success', 'Aula creada.');
    }

    public function show(Aula $aula): View
    {
        $this->authorize('sedes.ver');
        $aula->load('sede', 'edificio');
        return view('aulas.show', compact('aula'));
    }

    public function edit(Aula $aula): View
    {
        $this->authorize('sedes.editar');
        $orgId     = auth()->user()->organizacion_id;
        $sedes     = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        $edificios = Edificio::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->get();
        return view('aulas.edit', compact('aula', 'sedes', 'edificios'));
    }

    public function update(Request $request, Aula $aula): RedirectResponse
    {
        $this->authorize('sedes.editar');
        $data = $request->validate([
            'nombre'          => 'required|string|max:100',
            'tipo'            => 'required|in:salon,laboratorio,taller,sala_computo,sala_usos_multiples,auditorio',
            'capacidad'       => 'required|integer|min:1',
            'piso'            => 'nullable|integer',
            'tiene_proyector' => 'boolean',
            'tiene_ac'        => 'boolean',
            'activa'          => 'boolean',
        ]);
        $before = $aula->toArray();
        $aula->update($data);
        $this->audit->log(modulo: 'catalogos', accion: 'update', model: Aula::class, modelId: $aula->id,
            before: $before, after: $aula->fresh()->toArray());
        return redirect()->route('aulas.show', $aula)->with('success', 'Aula actualizada.');
    }

    public function destroy(Aula $aula): RedirectResponse
    {
        $this->authorize('sedes.editar');
        $aula->delete();
        $this->audit->log(modulo: 'catalogos', accion: 'delete', model: Aula::class, modelId: $aula->id);
        return redirect()->route('aulas.index')->with('success', 'Aula eliminada.');
    }
}
