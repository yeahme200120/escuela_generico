<?php namespace App\Http\Controllers;
use App\Models\Sede;
use Illuminate\Http\Request;
class SedeController extends Controller {
    public function index() { $items=Sede::with('escuela')->paginate(15); return view('sedes.index',['items'=>$items]); }
    public function create() { return view('sedes.create'); }
    public function store(Request $r) { $d=$r->validate(['nombre'=>'required','escuela_id'=>'required|exists:escuelas,id','direccion'=>'nullable','ciudad'=>'nullable']); Sede::create($d); return redirect()->route('sedes.index')->with('success','Sede creada'); }
    public function show(Sede $s) { return view('sedes.show',['sede'=>$s]); }
    public function edit(Sede $s) { return view('sedes.edit',['sede'=>$s]); }
    public function update(Request $r, Sede $s) { $d=$r->validate(['nombre'=>'required','direccion'=>'nullable','ciudad'=>'nullable']); $s->update($d); return redirect()->route('sedes.index')->with('success','Actualizado'); }
    public function destroy(Sede $s) { $s->delete(); return back()->with('success','Eliminado'); }
}
