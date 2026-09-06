<?php

namespace App\Http\Controllers;

use App\Models\Edificio;
use App\Models\Sede;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EdificioController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('sedes.ver');
        $orgId     = auth()->user()->organizacion_id;
        $edificios = Edificio::with('sede')
            ->whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->sede_id, fn($q, $id) => $q->where('sede_id', $id))
            ->paginate(25)->withQueryString();
        $sedes = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        return view('edificios.index', compact('edificios', 'sedes'));
    }

    public function create(): View
    {
        $this->authorize('sedes.editar');
        $orgId = auth()->user()->organizacion_id;
        $sedes = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        return view('edificios.create', compact('sedes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('sedes.editar');
        $data = $request->validate([
            'sede_id'       => 'required|exists:sedes,id',
            'nombre'        => 'required|string|max:100',
            'clave'         => 'nullable|string|max:30',
            'numero_pisos'  => 'required|integer|min:1',
            'descripcion'   => 'nullable|string|max:300',
        ]);
        $edificio = Edificio::create($data);
        $this->audit->log(modulo: 'catalogos', accion: 'create', model: Edificio::class, modelId: $edificio->id,
            descripcion: "Edificio: {$edificio->nombre}");
        return redirect()->route('edificios.index')->with('success', 'Edificio creado.');
    }

    public function show(Edificio $edificio): View
    {
        $this->authorize('sedes.ver');
        $edificio->load('sede', 'aulas');
        return view('edificios.show', compact('edificio'));
    }

    public function edit(Edificio $edificio): View
    {
        $this->authorize('sedes.editar');
        $orgId = auth()->user()->organizacion_id;
        $sedes = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        return view('edificios.edit', compact('edificio', 'sedes'));
    }

    public function update(Request $request, Edificio $edificio): RedirectResponse
    {
        $this->authorize('sedes.editar');
        $data = $request->validate([
            'nombre'       => 'required|string|max:100',
            'clave'        => 'nullable|string|max:30',
            'numero_pisos' => 'required|integer|min:1',
            'descripcion'  => 'nullable|string|max:300',
            'activo'       => 'boolean',
        ]);
        $before = $edificio->toArray();
        $edificio->update($data);
        $this->audit->log(modulo: 'catalogos', accion: 'update', model: Edificio::class, modelId: $edificio->id,
            before: $before, after: $edificio->fresh()->toArray());
        return redirect()->route('edificios.show', $edificio)->with('success', 'Edificio actualizado.');
    }

    public function destroy(Edificio $edificio): RedirectResponse
    {
        $this->authorize('sedes.editar');
        $edificio->delete();
        $this->audit->log(modulo: 'catalogos', accion: 'delete', model: Edificio::class, modelId: $edificio->id);
        return redirect()->route('edificios.index')->with('success', 'Edificio eliminado.');
    }
}
