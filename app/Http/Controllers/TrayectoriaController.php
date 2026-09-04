<?php namespace App\Http\Controllers;
use App\Models\TrayectoriaAlumno;
use Illuminate\Http\Request;
class TrayectoriaController extends Controller {
    public function index() { $items=TrayectoriaAlumno::with('alumno')->paginate(15); return view('trayectorias.index',['items'=>$items]); }
    public function create() { return view('trayectorias.create'); }
    public function store(Request $r) { $d=$r->validate(['alumno_id'=>'required|exists:alumnos,id','evento'=>'required','fecha'=>'required|date','descripcion'=>'nullable']); TrayectoriaAlumno::create($d); return redirect()->route('trayectorias.index')->with('success','Evento registrado'); }
    public function show(TrayectoriaAlumno $t) { return view('trayectorias.show',['trayectoria'=>$t]); }
    public function edit(TrayectoriaAlumno $t) { return view('trayectorias.edit',['trayectoria'=>$t]); }
    public function update(Request $r, TrayectoriaAlumno $t) { $d=$r->validate(['evento'=>'required','fecha'=>'required|date']); $t->update($d); return redirect()->route('trayectorias.index')->with('success','Actualizado'); }
    public function destroy(TrayectoriaAlumno $t) { $t->delete(); return back()->with('success','Eliminado'); }
}
