<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        $items = Notificacion::paginate(15);
        return view('notificaciones.index', ['items' => $items]);
    }
    public function create()
    {
        return view('notificaciones.create');
    }
    public function store(Request $r)
    {
        $d = $r->validate(['titulo' => 'required', 'mensaje' => 'required', 'tipo' => 'in:info,warning,error,success', 'destinatarios' => 'nullable']);
        Notificacion::create($d);
        return redirect()->route('notificaciones.index')->with('success', 'Notificaci�n creada');
    }
    public function show(Notificacion $n)
    {
        return view('notificaciones.show', ['notificacion' => $n]);
    }
    public function edit(Notificacion $n)
    {
        return view('notificaciones.edit', ['notificacion' => $n]);
    }
    public function update(Request $r, Notificacion $n)
    {
        $d = $r->validate(['titulo' => 'required', 'mensaje' => 'required', 'tipo' => 'in:info,warning,error,success']);
        $n->update($d);
        return redirect()->route('notificaciones.index')->with('success', 'Actualizado');
    }
    public function destroy(Notificacion $n)
    {
        $n->delete();
        return back()->with('success', 'Eliminado');
    }
}
