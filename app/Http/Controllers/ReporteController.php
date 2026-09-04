<?php namespace App\Http\Controllers;
use Illuminate\Http\Request;
class ReporteController extends Controller {
    public function index() { return view('reportes.index'); }
    public function create() { return view('reportes.create'); }
    public function store(Request $r) { $d=$r->validate(['titulo'=>'required','tipo'=>'in:alumnos,calificaciones,asistencias,finanzas','fecha_inicio'=>'required|date','fecha_fin'=>'required|date|after:fecha_inicio']); return redirect()->route('reportes.index')->with('success','Reporte despachado'); }
    public function show($id) { return view('reportes.show'); }
    public function edit($id) { return view('reportes.edit'); }
    public function update(Request $r, $id) { return redirect()->route('reportes.index')->with('success','Actualizado'); }
    public function destroy($id) { return back()->with('success','Eliminado'); }
}
