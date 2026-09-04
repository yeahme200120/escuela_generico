<?php namespace App\Http\Controllers;
use App\Models\Baja;
use Illuminate\Http\Request;
class BajaController extends Controller {
    public function index() { $items=Baja::with('alumno')->paginate(15); return view('bajas.index',['items'=>$items]); }
    public function create() { return view('bajas.create'); }
    public function store(Request $r) { $d=$r->validate(['alumno_id'=>'required|exists:alumnos,id','motivo'=>'required','fecha_baja'=>'required|date']); Baja::create($d); return redirect()->route('bajas.index')->with('success','Baja registrada'); }
    public function show(Baja $b) { return view('bajas.show',['baja'=>$b]); }
    public function edit(Baja $b) { return view('bajas.edit',['baja'=>$b]); }
    public function update(Request $r, Baja $b) { $d=$r->validate(['motivo'=>'required','fecha_baja'=>'required|date']); $b->update($d); return redirect()->route('bajas.index')->with('success','Actualizado'); }
    public function destroy(Baja $b) { $b->delete(); return back()->with('success','Eliminado'); }
}
