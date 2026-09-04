<?php namespace App\Http\Controllers;
use App\Models\Materia;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index() {
        $items = Materia::paginate(15);
        return view('materias.index', ['items' => $items]);
    }
    public function create() {
        return view('materias.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|unique:materias|max:100',
            'codigo' => 'nullable|string|unique:materias|max:20',
            'descripcion' => 'nullable|string|max:500',
            'horas_semanales' => 'nullable|numeric|min:1'
        ]);
        Materia::create($data);
        return redirect()->route('materias.index')->with('success', 'Materia creada');
    }
    public function show(Materia $materia) {
        return view('materias.show', compact('materia'));
    }
    public function edit(Materia $materia) {
        return view('materias.edit', compact('materia'));
    }
    public function update(Request $request, Materia $materia) {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:materias,nombre,'.$materia->id,
            'codigo' => 'nullable|string|max:20|unique:materias,codigo,'.$materia->id,
            'descripcion' => 'nullable|string|max:500',
            'horas_semanales' => 'nullable|numeric|min:1'
        ]);
        $materia->update($data);
        return redirect()->route('materias.index')->with('success', 'Actualizado');
    }
    public function destroy(Materia $materia) {
        $materia->delete();
        return back()->with('success', 'Eliminado');
    }
}
