<?php namespace App\Http\Controllers;
use App\Models\PlanEstudio;
use Illuminate\Http\Request;
class PlanEstudioController extends Controller {
    public function index() { $items=PlanEstudio::with('nivel','grado')->paginate(15); return view('planes.index',['items'=>$items]); }
    public function create() { return view('planes.create'); }
    public function store(Request $r) { $d=$r->validate(['nombre'=>'required','nivel_educativo_id'=>'required|exists:niveles_educativos,id','grado_id'=>'required|exists:grados,id']); PlanEstudio::create($d); return redirect()->route('planes.index')->with('success','Plan creado'); }
    public function show(PlanEstudio $p) { return view('planes.show',['plan'=>$p]); }
    public function edit(PlanEstudio $p) { return view('planes.edit',['plan'=>$p]); }
    public function update(Request $r, PlanEstudio $p) { $d=$r->validate(['nombre'=>'required']); $p->update($d); return redirect()->route('planes.index')->with('success','Actualizado'); }
    public function destroy(PlanEstudio $p) { $p->delete(); return back()->with('success','Eliminado'); }
}
