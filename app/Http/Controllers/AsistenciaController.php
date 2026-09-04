<?php namespace App\Http\Controllers;
use App\Models\Asistencia;
use App\Models\Grupo;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index() {
        $items = Asistencia::with(['alumno', 'grupo', 'periodo'])->paginate(20);
        return view('asistencias.index', ['items' => $items]);
    }
    public function create() {
        $grupos = Grupo::all();
        return view('asistencias.create', compact('grupos'));
    }
    public function store(Request $request) {
        $data = $request->validate([
            'alumno_id' => 'required|exists:alumnos,id',
            'grupo_id' => 'required|exists:grupos,id',
            'periodo_asistencia_id' => 'required|exists:periodos_asistencia,id',
            'fecha' => 'required|date',
            'presente' => 'boolean',
            'justificacion' => 'nullable|string|max:255'
        ]);
        Asistencia::create($data);
        return redirect()->route('asistencias.index')->with('success', 'Asistencia registrada');
    }
    public function show(Asistencia $asistencia) {
        return view('asistencias.show', compact('asistencia'));
    }
    public function edit(Asistencia $asistencia) {
        return view('asistencias.edit', compact('asistencia'));
    }
    public function update(Request $request, Asistencia $asistencia) {
        $data = $request->validate([
            'presente' => 'boolean',
            'justificacion' => 'nullable|string|max:255'
        ]);
        $asistencia->update($data);
        return redirect()->route('asistencias.index')->with('success', 'Actualizado');
    }
    public function destroy(Asistencia $asistencia) {
        $asistencia->delete();
        return back()->with('success', 'Eliminado');
    }
}
