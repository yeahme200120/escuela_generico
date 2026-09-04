<?php namespace App\Http\Controllers;
use App\Models\Aula;
use Illuminate\Http\Request;
class AulaController extends Controller {
    public function index() { $items=Aula::with('edificio')->paginate(15); return view('aulas.index',['items'=>$items]); }
    public function create() { return view('aulas.create'); }
    public function store(Request $r) { $d=$r->validate(['nombre'=>'required','edificio_id'=>'required|exists:edificios,id','capacidad'=>'nullable|integer']); Aula::create($d); return redirect()->route('aulas.index')->with('success','Aula creada'); }
    public function show(Aula $a) { return view('aulas.show',['aula'=>$a]); }
    public function edit(Aula $a) { return view('aulas.edit',['aula'=>$a]); }
    public function update(Request $r, Aula $a) { $d=$r->validate(['nombre'=>'required','capacidad'=>'nullable|integer']); $a->update($d); return redirect()->route('aulas.index')->with('success','Actualizado'); }
    public function destroy(Aula $a) { $a->delete(); return back()->with('success','Eliminado'); }
}
