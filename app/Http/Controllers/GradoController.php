<?php namespace App\Http\Controllers;
use App\Models\Grado;
use Illuminate\Http\Request;

class GradoController extends Controller
{
    public function index() {
        $items = Grado::with('nivel')->paginate(15);
        return view('grados.index', ['items' => $items]);
    }
    public function create() {
        return view('grados.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'nivel_educativo_id' => 'required|exists:niveles_educativos,id',
            'numero' => 'nullable|integer|min:1'
        ]);
        Grado::create($data);
        return redirect()->route('grados.index')->with('success', 'Grado creado');
    }
    public function show(Grado $grado) {
        return view('grados.show', compact('grado'));
    }
    public function edit(Grado $grado) {
        return view('grados.edit', compact('grado'));
    }
    public function update(Request $request, Grado $grado) {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'nivel_educativo_id' => 'required|exists:niveles_educativos,id',
            'numero' => 'nullable|integer|min:1'
        ]);
        $grado->update($data);
        return redirect()->route('grados.index')->with('success', 'Actualizado');
    }
    public function destroy(Grado $grado) {
        $grado->delete();
        return back()->with('success', 'Eliminado');
    }
}
