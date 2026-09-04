<?php namespace App\Http\Controllers;
use App\Models\ConceptoPago;
use Illuminate\Http\Request;

class ConceptoPagoController extends Controller
{
    public function index() {
        $items = ConceptoPago::paginate(15);
        return view('conceptos.index', ['items' => $items]);
    }
    public function create() {
        return view('conceptos.create');
    }
    public function store(Request $request) {
        $data = $request->validate([
            'nombre' => 'required|string|unique:conceptos_pago|max:100',
            'descripcion' => 'nullable|string|max:500',
            'monto' => 'required|numeric|min:0.01',
            'es_recurrente' => 'boolean',
            'periodo' => 'nullable|in:mensual,bimestral,trimestral,semestral,anual'
        ]);
        ConceptoPago::create($data);
        return redirect()->route('conceptos.index')->with('success', 'Concepto creado');
    }
    public function show(ConceptoPago $concepto) {
        return view('conceptos.show', compact('concepto'));
    }
    public function edit(ConceptoPago $concepto) {
        return view('conceptos.edit', compact('concepto'));
    }
    public function update(Request $request, ConceptoPago $concepto) {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:conceptos_pago,nombre,'.$concepto->id,
            'descripcion' => 'nullable|string|max:500',
            'monto' => 'required|numeric|min:0.01',
            'es_recurrente' => 'boolean',
            'periodo' => 'nullable|in:mensual,bimestral,trimestral,semestral,anual'
        ]);
        $concepto->update($data);
        return redirect()->route('conceptos.index')->with('success', 'Actualizado');
    }
    public function destroy(ConceptoPago $concepto) {
        $concepto->delete();
        return back()->with('success', 'Eliminado');
    }
}
