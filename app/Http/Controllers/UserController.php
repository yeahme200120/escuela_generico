<?php namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {
        $items = User::with(['roles'])->paginate(15);
        return view('users.index', ['items' => $items]);
    }
    public function create() {
        $roles = \App\Models\Role::all();
        return view('users.create', compact('roles'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'email' => 'required|email|unique:users',
            'nombre' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'activo' => 'boolean'
        ]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('users.index')->with('success', 'Usuario creado');
    }
    public function show(User $user) {
        return view('users.show', compact('user'));
    }
    public function edit(User $user) {
        $roles = \App\Models\Role::all();
        return view('users.edit', compact('user', 'roles'));
    }
    public function update(Request $request, User $user) {
        $data = $request->validate([
            'email' => 'required|email|unique:users,email,'.$user->id,
            'nombre' => 'required|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
            'activo' => 'boolean'
        ]);
        if ($data['password']) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('users.index')->with('success', 'Actualizado');
    }
    public function destroy(User $user) {
        if ($user->id !== auth()->id()) {
            $user->delete();
            return back()->with('success', 'Eliminado');
        }
        return back()->with('error', 'No puedes eliminarte a ti mismo');
    }
}
