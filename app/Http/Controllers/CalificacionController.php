<?php namespace App\Http\Controllers;
use App\Models\Calificacion;
use Illuminate\Http\Request;

class CalificacionController extends Controller
{
    public function index() {
        $items = Calificacion::with(['alumno', 'materia', 'periodo'])->paginate(20);
        return view('calificaciones.index', ['items' => $items]);
    }
    public function create() {
        return view('calificaciones.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'materia_id' => 'required|exists:materias,id',
            'periodo_evaluacion_id' => 'required|exists:periodos_evaluacion,id',
            'calificacion' => 'required|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:500'
        ]);
        Calificacion::create($data);
        return redirect()->route('calificaciones.index')->with('success', 'Calificación registrada');
    }
    public function show(Calificacion $calificacion) {
        return view('calificaciones.show', compact('calificacion'));
    }
    public function edit(Calificacion $calificacion) {
        return view('calificaciones.edit', compact('calificacion'));
    }
    public function update(Request $request, Calificacion $calificacion) {
        $data = $request->validate([
            'calificacion' => 'required|numeric|min:0|max:100',
            'observaciones' => 'nullable|string|max:500'
        ]);
        $calificacion->update($data);
        return redirect()->route('calificaciones.index')->with('success', 'Actualizado');
    }
    public function destroy(Calificacion $calificacion) {
        $calificacion->delete();
        return back()->with('success', 'Eliminado');
    }
}
