<?php namespace App\Http\Controllers;
use App\Models\Escuela;
use Illuminate\Http\Request;
class EscuelaController extends Controller {
    public function index() { $items=Escuela::with('organizacion')->paginate(15); return view('escuelas.index',['items'=>$items]); }
    public function create() { return view('escuelas.create'); }
    public function store(Request $r) { $d=$r->validate(['nombre'=>'required','organizacion_id'=>'required|exists:organizaciones,id','email'=>'nullable|email']); Escuela::create($d); return redirect()->route('escuelas.index')->with('success','Escuela creada'); }
    public function show(Escuela $e) { return view('escuelas.show',['escuela'=>$e]); }
    public function edit(Escuela $e) { return view('escuelas.edit',['escuela'=>$e]); }
    public function update(Request $r, Escuela $e) { $d=$r->validate(['nombre'=>'required','email'=>'nullable|email']); $e->update($d); return redirect()->route('escuelas.index')->with('success','Actualizado'); }
    public function destroy(Escuela $e) { $e->delete(); return back()->with('success','Eliminado'); }
}
