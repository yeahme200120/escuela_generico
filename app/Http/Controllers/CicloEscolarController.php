<?php namespace App\Http\Controllers;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;
class CicloEscolarController extends Controller {
    public function index() { $items=CicloEscolar::paginate(15); return view('ciclos.index',['items'=>$items]); }
    public function create() { return view('ciclos.create'); }
    public function store(Request $r) { $d=$r->validate(['nombre'=>'required|unique:ciclos_escolares','fecha_inicio'=>'required|date','fecha_fin'=>'required|date|after:fecha_inicio']); CicloEscolar::create($d); return redirect()->route('ciclos.index')->with('success','Ciclo creado'); }
    public function show(CicloEscolar $c) { return view('ciclos.show',['ciclo'=>$c]); }
    public function edit(CicloEscolar $c) { return view('ciclos.edit',['ciclo'=>$c]); }
    public function update(Request $r, CicloEscolar $c) { $d=$r->validate(['nombre'=>'required','fecha_inicio'=>'required|date','fecha_fin'=>'required|date|after:fecha_inicio']); $c->update($d); return redirect()->route('ciclos.index')->with('success','Actualizado'); }
    public function destroy(CicloEscolar $c) { $c->delete(); return back()->with('success','Eliminado'); }
}
