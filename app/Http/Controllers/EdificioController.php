<?php namespace App\Http\Controllers;
use App\Models\Edificio;
use Illuminate\Http\Request;
class EdificioController extends Controller {
    public function index() { $items=Edificio::paginate(15); return view('edificios.index',['items'=>$items]); }
    public function create() { return view('edificios.create'); }
    public function store(Request $r) { $d=$r->validate(['nombre'=>'required','pisos'=>'nullable|integer']); Edificio::create($d); return redirect()->route('edificios.index')->with('success','Edificio creado'); }
    public function show(Edificio $e) { return view('edificios.show',['edificio'=>$e]); }
    public function edit(Edificio $e) { return view('edificios.edit',['edificio'=>$e]); }
    public function update(Request $r, Edificio $e) { $d=$r->validate(['nombre'=>'required','pisos'=>'nullable|integer']); $e->update($d); return redirect()->route('edificios.index')->with('success','Actualizado'); }
    public function destroy(Edificio $e) { $e->delete(); return back()->with('success','Eliminado'); }
}
