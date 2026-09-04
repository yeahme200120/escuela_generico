<?php namespace App\Http\Controllers;
use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index() {
        $items = Horario::with('grupo')->paginate(15);
        return view('horarios.index', ['items' => $items]);
    }
    public function create() {
        return view('horarios.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'dia' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'aula_id' => 'required|exists:aulas,id'
        ]);
        Horario::create($data);
        return redirect()->route('horarios.index')->with('success', 'Horario creado');
    }
    public function show(Horario $horario) {
        return view('horarios.show', compact('horario'));
    }
    public function edit(Horario $horario) {
        return view('horarios.edit', compact('horario'));
    }
    public function update(Request $request, Horario $horario) {
        $data = $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'dia' => 'required',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'aula_id' => 'required|exists:aulas,id'
        ]);
        $horario->update($data);
        return redirect()->route('horarios.index')->with('success', 'Actualizado');
    }
    public function destroy(Horario $horario) {
        $horario->delete();
        return back()->with('success', 'Eliminado');
    }
}
