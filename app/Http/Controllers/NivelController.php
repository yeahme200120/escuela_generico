<?php namespace App\Http\Controllers;
use App\Models\NivelEducativo;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function index() {
        $items = NivelEducativo::paginate(15);
        return view('niveles.index', ['items' => $items]);
    }
    public function create() {
        return view('niveles.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|unique:niveles_educativos|max:100',
            'descripcion' => 'nullable|string|max:500',
            'orden' => 'nullable|integer'
        ]);
        NivelEducativo::create($data);
        return redirect()->route('niveles.index')->with('success', 'Nivel creado');
    }
    public function show(NivelEducativo $nivel) {
        return view('niveles.show', compact('nivel'));
    }
    public function edit(NivelEducativo $nivel) {
        return view('niveles.edit', compact('nivel'));
    }
    public function update(Request $request, NivelEducativo $nivel) {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:niveles_educativos,nombre,'.$nivel->id,
            'descripcion' => 'nullable|string|max:500',
            'orden' => 'nullable|integer'
        ]);
        $nivel->update($data);
        return redirect()->route('niveles.index')->with('success', 'Actualizado');
    }
    public function destroy(NivelEducativo $nivel) {
        $nivel->delete();
        return back()->with('success', 'Eliminado');
    }
}
