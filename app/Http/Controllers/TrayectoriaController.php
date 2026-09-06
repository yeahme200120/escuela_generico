<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\TrayectoriaAlumno;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrayectoriaController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('control_escolar.ver');
        $orgId = auth()->user()->organizacion_id;
        $trayectorias = TrayectoriaAlumno::with(['alumno', 'cicloEscolar', 'grado', 'grupo', 'sede'])
            ->whereHas('alumno', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->alumno_id, fn($q, $id) => $q->where('alumno_id', $id))
            ->when($request->ciclo_id,  fn($q, $id) => $q->where('ciclo_escolar_id', $id))
            ->when($request->estatus,   fn($q, $e)  => $q->where('estatus', $e))
            ->orderByDesc('fecha_inicio')
            ->paginate(30)->withQueryString();
        return view('trayectorias.index', compact('trayectorias'));
    }

    public function show(int $alumnoId): View
    {
        $this->authorize('control_escolar.ver');
        $orgId  = auth()->user()->organizacion_id;
        $alumno = Alumno::where('organizacion_id', $orgId)->findOrFail($alumnoId);
        $trayectorias = TrayectoriaAlumno::with(['cicloEscolar', 'grado', 'grupo', 'sede', 'usuario'])
            ->where('alumno_id', $alumnoId)
            ->orderByDesc('fecha_inicio')->get();
        return view('trayectorias.show', compact('alumno', 'trayectorias'));
    }
}
