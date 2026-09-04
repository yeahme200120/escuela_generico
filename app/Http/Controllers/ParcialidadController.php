<?php namespace App\Http\Controllers;
use App\Models\Parcialidad;
use Illuminate\Http\Request;

class ParcialidadController extends Controller
{
    public function index() {
        $items = Parcialidad::with(['concepto', 'alumno'])->paginate(15);
        return view('parcialidades.index', ['items' => $items]);
    }
    public function create() {
        return view('parcialidades.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'concepto_pago_id' => 'required|exists:conceptos_pago,id',
            'alumno_id' => 'required|exists:alumnos,id',
            'numero_parcialidad' => 'required|integer|min:1',
            'total_parcialidades' => 'required|integer|min:1',
            'monto' => 'required|numeric|min:0.01',
            'fecha_vencimiento' => 'required|date',
            'estado' => 'in:pendiente,pagado,vencido'
        ]);
        Parcialidad::create($data);
        return redirect()->route('parcialidades.index')->with('success', 'Parcialidad creada');
    }
    public function show(Parcialidad $parcialidad) {
        return view('parcialidades.show', compact('parcialidad'));
    }
    public function edit(Parcialidad $parcialidad) {
        return view('parcialidades.edit', compact('parcialidad'));
    }
    public function update(Request $request, Parcialidad $parcialidad) {
        $data = $request->validate([
            'estado' => 'in:pendiente,pagado,vencido',
            'fecha_vencimiento' => 'required|date'
        ]);
        $parcialidad->update($data);
        return redirect()->route('parcialidades.index')->with('success', 'Actualizado');
    }
    public function destroy(Parcialidad $parcialidad) {
        $parcialidad->delete();
        return back()->with('success', 'Eliminado');
    }
}
