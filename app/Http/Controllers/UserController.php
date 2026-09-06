<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\Role;
use App\Models\Sede;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserSede;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('usuarios.ver');
        $orgId = auth()->user()->organizacion_id;

        $users = User::where('organizacion_id', $orgId)
            ->with('roles')
            ->when($request->q, fn($q, $s) => $q->where(fn($sub) =>
                $sub->where('nombres', 'like', "%$s%")
                    ->orWhere('apellido_paterno', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
                    ->orWhere('username', 'like', "%$s%")
            ))
            ->when($request->filled('activo'), fn($q) => $q->where('activo', (bool)$request->activo))
            ->orderBy('apellido_paterno')
            ->paginate(25)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorize('usuarios.crear');
        $orgId = auth()->user()->organizacion_id;
        $roles = Role::activos()->orderBy('nivel')->get();
        $sedes = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        return view('users.create', compact('roles', 'sedes'));
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['organizacion_id'] = auth()->user()->organizacion_id;
        $data['password']        = Hash::make($data['password']);

        $user = User::create($data);

        // Asignar roles
        foreach ($request->roles ?? [] as $roleId) {
            UserRole::create(['user_id' => $user->id, 'role_id' => $roleId, 'activo' => true]);
        }
        // Asignar sedes
        foreach ($request->sede_ids ?? [] as $i => $sedeId) {
            UserSede::create(['user_id' => $user->id, 'sede_id' => $sedeId, 'es_principal' => $i === 0, 'activo' => true]);
        }

        $this->audit->log(modulo: 'usuarios', accion: 'create', model: User::class, modelId: $user->id,
            descripcion: "Usuario creado: {$user->email}");

        return redirect()->route('users.index')->with('success', "Usuario {$user->email} creado correctamente.");
    }

    public function show(User $user): View
    {
        $this->authorize('usuarios.ver');
        $this->verificarMismaOrg($user);
        $user->load('roles', 'sedes', 'userSessions', 'accessLogs');
        return view('users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        $this->authorize('usuarios.editar');
        $this->verificarMismaOrg($user);
        $orgId = auth()->user()->organizacion_id;
        $roles = Role::activos()->orderBy('nivel')->get();
        $sedes = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        return view('users.edit', compact('user', 'roles', 'sedes'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $this->verificarMismaOrg($user);
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $before = $user->toArray();
        $user->update($data);

        $this->audit->log(modulo: 'usuarios', accion: 'update', model: User::class, modelId: $user->id,
            before: $before, after: $user->fresh()->toArray(),
            descripcion: "Usuario actualizado: {$user->email}");

        return redirect()->route('users.show', $user)->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('usuarios.eliminar');
        $this->verificarMismaOrg($user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();
        $this->audit->log(modulo: 'usuarios', accion: 'delete', model: User::class, modelId: $user->id,
            descripcion: "Usuario eliminado: {$user->email}");

        return redirect()->route('users.index')->with('success', 'Usuario eliminado.');
    }

    private function verificarMismaOrg(User $user): void
    {
        if ($user->organizacion_id !== auth()->user()->organizacion_id) {
            abort(403, 'No pertenece a tu organización.');
        }
    }
}
