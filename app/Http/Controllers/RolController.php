<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\RolRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RolController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('roles.ver');
        $roles = Role::with('permissions')
            ->when($request->q, fn($q, $s) => $q->where('nombre', 'like', "%$s%"))
            ->orderBy('nivel')->paginate(20)->withQueryString();
        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('roles.asignar');
        $permisos = Permission::activos()->orderBy('modulo')->orderBy('accion')->get()->groupBy('modulo');
        return view('roles.create', compact('permisos'));
    }

    public function store(RolRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['es_sistema']      = false;
        $data['organizacion_id'] = auth()->user()->organizacion_id;
        $permisos = $data['permisos'] ?? [];
        unset($data['permisos']);

        $rol = Role::create($data);
        if ($permisos) {
            $sync = collect($permisos)->mapWithKeys(fn($id) => [$id => ['alcance' => 'sede']])->toArray();
            $rol->permissions()->sync($sync);
        }

        $this->audit->log(modulo: 'roles', accion: 'create', model: Role::class, modelId: $rol->id,
            descripcion: "Rol creado: {$rol->nombre}");

        return redirect()->route('roles.index')->with('success', "Rol '{$rol->nombre}' creado.");
    }

    public function show(Role $rol): View
    {
        $this->authorize('roles.ver');
        $rol->load('permissions', 'users');
        return view('roles.show', compact('rol'));
    }

    public function edit(Role $rol): View
    {
        $this->authorize('roles.asignar');
        if ($rol->es_sistema) abort(403, 'Los roles del sistema no se pueden modificar.');
        $permisos = Permission::activos()->orderBy('modulo')->orderBy('accion')->get()->groupBy('modulo');
        $asignados = $rol->permissions->pluck('id')->toArray();
        return view('roles.edit', compact('rol', 'permisos', 'asignados'));
    }

    public function update(RolRequest $request, Role $rol): RedirectResponse
    {
        if ($rol->es_sistema) abort(403);
        $data = $request->validated();
        $permisos = $data['permisos'] ?? [];
        unset($data['permisos']);

        $before = $rol->toArray();
        $rol->update($data);

        if ($permisos) {
            $sync = collect($permisos)->mapWithKeys(fn($id) => [$id => ['alcance' => 'sede']])->toArray();
            $rol->permissions()->sync($sync);
        }

        $this->audit->log(modulo: 'roles', accion: 'update', model: Role::class, modelId: $rol->id,
            before: $before, after: $rol->fresh()->toArray());

        return redirect()->route('roles.show', $rol)->with('success', 'Rol actualizado.');
    }

    public function destroy(Role $rol): RedirectResponse
    {
        $this->authorize('roles.asignar');
        if ($rol->es_sistema) abort(403, 'Los roles del sistema no se pueden eliminar.');

        $rol->delete();
        $this->audit->log(modulo: 'roles', accion: 'delete', model: Role::class, modelId: $rol->id);
        return redirect()->route('roles.index')->with('success', 'Rol eliminado.');
    }
}
