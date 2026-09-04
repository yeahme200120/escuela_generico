<?php namespace App\Http\Controllers;
use App\Models\Organizacion;
use Illuminate\Http\Request;

class OrganizacionController extends Controller {
    public function index() { 
        $items = Organizacion::paginate(15);
        return view('organizaciones.index', ['items' => $items]); 
    }
    public function create() { return view('organizaciones.create'); }
    public function store(Request $r) { 
        $d = $r->validate(['nombre'=>'required|unique:organizaciones','ruc'=>'nullable|unique:organizaciones','email'=>'nullable|email']);
        Organizacion::create($d);
        return redirect()->route('organizaciones.index')->with('success','Creado'); 
    }
    public function show(Organizacion $o) { return view('organizaciones.show', ['organizacion'=>$o]); }
    public function edit(Organizacion $o) { return view('organizaciones.edit', ['organizacion'=>$o]); }
    public function update(Request $r, Organizacion $o) { 
        $d = $r->validate(['nombre'=>'required','email'=>'nullable|email']);
        $o->update($d);
        return redirect()->route('organizaciones.index')->with('success','Actualizado'); 
    }
    public function destroy(Organizacion $o) { $o->delete(); return back()->with('success','Eliminado'); }
}
