<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\OrganizacionRequest;
use App\Models\Organizacion;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizacionController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('sedes.ver'); // Superadmin ve todas
        $orgs = Organizacion::when($request->q, fn($q, $s) => $q->where('nombre', 'like', "%$s%"))
            ->orderBy('nombre')->paginate(20)->withQueryString();
        return view('organizaciones.index', compact('orgs'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->esSuperadmin(), 403);
        return view('organizaciones.create');
    }

    public function store(OrganizacionRequest $request): RedirectResponse
    {
        $org = Organizacion::create($request->validated());
        $this->audit->log(modulo: 'organizaciones', accion: 'create', model: Organizacion::class, modelId: $org->id,
            descripcion: "Organización creada: {$org->nombre}");
        return redirect()->route('organizaciones.show', $org)->with('success', 'Organización creada.');
    }

    public function show(Organizacion $organizacion): View
    {
        $this->authorize('sedes.ver');
        $organizacion->load('escuelas', 'sedes');
        return view('organizaciones.show', ['org' => $organizacion]);
    }

    public function edit(Organizacion $organizacion): View
    {
        abort_unless(auth()->user()->esSuperadmin(), 403);
        return view('organizaciones.edit', ['org' => $organizacion]);
    }

    public function update(OrganizacionRequest $request, Organizacion $organizacion): RedirectResponse
    {
        $before = $organizacion->toArray();
        $organizacion->update($request->validated());
        $this->audit->log(modulo: 'organizaciones', accion: 'update', model: Organizacion::class, modelId: $organizacion->id,
            before: $before, after: $organizacion->fresh()->toArray());
        return redirect()->route('organizaciones.show', $organizacion)->with('success', 'Organización actualizada.');
    }

    public function destroy(Organizacion $organizacion): RedirectResponse
    {
        abort_unless(auth()->user()->esSuperadmin(), 403);
        $organizacion->update(['activa' => false]);
        $this->audit->log(modulo: 'organizaciones', accion: 'delete', model: Organizacion::class, modelId: $organizacion->id);
        return redirect()->route('organizaciones.index')->with('success', 'Organización desactivada.');
    }
}
