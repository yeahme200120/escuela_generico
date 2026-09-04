<?php namespace App\Http\Controllers\RH;
use App\Models\Empleado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmpleadoController extends Controller
{
    public function index() {
        $items = Empleado::with(['contrato'])->paginate(15);
        return view('rh.empleados.index', ['items' => $items]);
    }
    public function create() {
        return view('rh.empleados.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:empleados',
            'telefono' => 'nullable|string|max:15',
            'puesto' => 'required|string|max:100',
            'fecha_ingreso' => 'required|date',
            'estado' => 'in:activo,inactivo,permiso,licencia'
        ]);
        Empleado::create($data);
        return redirect()->route('rh.empleados.index')->with('success', 'Empleado creado');
    }
    public function show(Empleado $empleado) {
        return view('rh.empleados.show', compact('empleado'));
    }
    public function edit(Empleado $empleado) {
        return view('rh.empleados.edit', compact('empleado'));
    }
    public function update(Request $request, Empleado $empleado) {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:empleados,email,'.$empleado->id,
            'telefono' => 'nullable|string|max:15',
            'puesto' => 'required|string|max:100',
            'estado' => 'in:activo,inactivo,permiso,licencia'
        ]);
        $empleado->update($data);
        return redirect()->route('rh.empleados.index')->with('success', 'Actualizado');
    }
    public function destroy(Empleado $empleado) {
        $empleado->delete();
        return back()->with('success', 'Eliminado');
    }
}
