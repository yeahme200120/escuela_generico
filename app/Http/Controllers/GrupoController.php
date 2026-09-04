<?php namespace App\Http\Controllers;
use App\Models\Grupo;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index() {
        $items = Grupo::with(['grado', 'nivel', 'cicloEscolar'])->paginate(15);
        return view('grupos.index', ['items' => $items]);
    }
    public function create() {
        return view('grupos.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|max:50',
            'grado_id' => 'required|exists:grados,id',
            'ciclo_escolar_id' => 'required|exists:ciclos_escolares,id',
            'capacidad_maxima' => 'nullable|integer|min:1|max:100',
            'turno' => 'nullable|in:matutino,vespertino,nocturno'
        ]);
        Grupo::create($data);
        return redirect()->route('grupos.index')->with('success', 'Grupo creado');
    }
    public function show(Grupo $grupo) {
        return view('grupos.show', compact('grupo'));
    }
    public function edit(Grupo $grupo) {
        return view('grupos.edit', compact('grupo'));
    }
    public function update(Request $request, Grupo $grupo) {
        $data = $request->validate([
            'nombre' => 'required|string|max:50',
            'grado_id' => 'required|exists:grados,id',
            'capacidad_maxima' => 'nullable|integer|min:1|max:100',
            'turno' => 'nullable|in:matutino,vespertino,nocturno'
        ]);
        $grupo->update($data);
        return redirect()->route('grupos.index')->with('success', 'Actualizado');
    }
    public function destroy(Grupo $grupo) {
        $grupo->delete();
        return back()->with('success', 'Eliminado');
    }
}
