<?php namespace App\Http\Controllers;
use App\Models\Tutor;
use Illuminate\Http\Request;
class TutorController extends Controller {
    public function index() { $items=Tutor::paginate(15); return view('tutores.index',['items'=>$items]); }
    public function create() { return view('tutores.create'); }
    public function store(Request $r) { $d=$r->validate(['nombre'=>'required','email'=>'nullable|email','telefono'=>'nullable','relacion'=>'nullable']); Tutor::create($d); return redirect()->route('tutores.index')->with('success','Tutor creado'); }
    public function show(Tutor $t) { return view('tutores.show',['tutor'=>$t]); }
    public function edit(Tutor $t) { return view('tutores.edit',['tutor'=>$t]); }
    public function update(Request $r, Tutor $t) { $d=$r->validate(['nombre'=>'required','email'=>'nullable|email','telefono'=>'nullable']); $t->update($d); return redirect()->route('tutores.index')->with('success','Actualizado'); }
    public function destroy(Tutor $t) { $t->delete(); return back()->with('success','Eliminado'); }
}
