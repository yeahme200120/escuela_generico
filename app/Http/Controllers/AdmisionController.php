<?php namespace App\Http\Controllers;
use App\Models\Admision;
use Illuminate\Http\Request;
class AdmisionController extends Controller {
    public function index() { $items=Admision::with('prospecto')->paginate(15); return view('admisiones.index',['items'=>$items]); }
    public function create() { return view('admisiones.create'); }
    public function store(Request $r) { $d=$r->validate(['prospecto_id'=>'required|exists:prospectos,id','alumno_id'=>'nullable|exists:alumnos,id','grupo_id'=>'required|exists:grupos,id','fecha_admision'=>'required|date']); Admision::create($d); return redirect()->route('admisiones.index')->with('success','Admisión registrada'); }
    public function show(Admision $a) { return view('admisiones.show',['admision'=>$a]); }
    public function edit(Admision $a) { return view('admisiones.edit',['admision'=>$a]); }
    public function update(Request $r, Admision $a) { $d=$r->validate(['grupo_id'=>'required|exists:grupos,id','fecha_admision'=>'required|date']); $a->update($d); return redirect()->route('admisiones.index')->with('success','Actualizado'); }
    public function destroy(Admision $a) { $a->delete(); return back()->with('success','Eliminado'); }
}
