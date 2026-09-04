<?php namespace App\Http\Controllers;
use App\Models\CalendarioEscolar;
use Illuminate\Http\Request;
class CalendarioController extends Controller {
    public function index() { $items=CalendarioEscolar::paginate(15); return view('calendario.index',['items'=>$items]); }
    public function create() { return view('calendario.create'); }
    public function store(Request $r) { $d=$r->validate(['titulo'=>'required','fecha'=>'required|date','tipo'=>'in:festivo,evento,cierre','descripcion'=>'nullable']); CalendarioEscolar::create($d); return redirect()->route('calendario.index')->with('success','Evento calendario creado'); }
    public function show(CalendarioEscolar $c) { return view('calendario.show',['calendario'=>$c]); }
    public function edit(CalendarioEscolar $c) { return view('calendario.edit',['calendario'=>$c]); }
    public function update(Request $r, CalendarioEscolar $c) { $d=$r->validate(['titulo'=>'required','fecha'=>'required|date']); $c->update($d); return redirect()->route('calendario.index')->with('success','Actualizado'); }
    public function destroy(CalendarioEscolar $c) { $c->delete(); return back()->with('success','Eliminado'); }
}
