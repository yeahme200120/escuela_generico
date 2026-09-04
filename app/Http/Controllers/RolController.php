<?php namespace App\Http\Controllers;
use App\Models\Role;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index() {
        $items = Role::withCount('permissions')->paginate(15);
        return view('roles.index', ['items' => $items]);
    }
    public function create() {
        return view('roles.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|unique:roles|max:100',
            'slug' => 'required|string|unique:roles|max:100',
            'descripcion' => 'nullable|string|max:500'
        ]);
        Role::create($data);
        return redirect()->route('roles.index')->with('success', 'Rol creado');
    }
    public function show(Role $role) {
        $permisos = $role->permissions;
        return view('roles.show', compact('role', 'permisos'));
    }
    public function edit(Role $role) {
        $todosPermisos = \App\Models\Permission::all();
        return view('roles.edit', compact('role', 'todosPermisos'));
    }
    public function update(Request $request, Role $role) {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:roles,nombre,'.$role->id,
            'descripcion' => 'nullable|string|max:500'
        ]);
        $role->update($data);
        return redirect()->route('roles.index')->with('success', 'Actualizado');
    }
    public function destroy(Role $role) {
        $role->delete();
        return back()->with('success', 'Eliminado');
    }
}
