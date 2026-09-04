<?php namespace App\Http\Controllers;
use App\Models\Docente;
use Illuminate\Http\Request;
class DocenteController extends Controller {
    public function index() { $items=Docente::paginate(15); return view('docentes.index',['items'=>$items]); }
    public function create() { return view('docentes.create'); }
    public function store(Request $r) { $d=$r->validate(['nombre'=>'required','email'=>'nullable|email','telefono'=>'nullable','especialidad'=>'nullable']); Docente::create($d); return redirect()->route('docentes.index')->with('success','Docente creado'); }
    public function show(Docente $d) { return view('docentes.show',['docente'=>$d]); }
    public function edit(Docente $d) { return view('docentes.edit',['docente'=>$d]); }
    public function update(Request $r, Docente $d) { $dat=$r->validate(['nombre'=>'required','email'=>'nullable|email','especialidad'=>'nullable']); $d->update($dat); return redirect()->route('docentes.index')->with('success','Actualizado'); }
    public function destroy(Docente $d) { $d->delete(); return back()->with('success','Eliminado'); }
}
