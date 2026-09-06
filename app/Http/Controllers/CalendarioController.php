<?php

namespace App\Http\Controllers;

use App\Models\CalendarioEscolar;
use App\Models\CicloEscolar;
use App\Models\Sede;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarioController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $orgId  = auth()->user()->organizacion_id;
        $sedes  = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        $ciclos = CicloEscolar::where('organizacion_id', $orgId)->orderByDesc('es_actual')->get();

        $eventos = CalendarioEscolar::with('sede')
            ->whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->sede_id, fn($q, $id) => $q->where('sede_id', $id))
            ->when($request->mes, fn($q, $m) => $q->whereMonth('fecha_inicio', $m))
            ->orderBy('fecha_inicio')
            ->paginate(30)->withQueryString();

        return view('calendario.index', compact('eventos', 'sedes', 'ciclos'));
    }

    public function create(): View
    {
        $orgId  = auth()->user()->organizacion_id;
        $sedes  = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        $ciclos = CicloEscolar::where('organizacion_id', $orgId)->activo()->get();
        return view('calendario.create', compact('sedes', 'ciclos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'sede_id'          => 'required|exists:sedes,id',
            'ciclo_escolar_id' => 'nullable|exists:ciclos_escolares,id',
            'titulo'           => 'required|string|max:200',
            'descripcion'      => 'nullable|string|max:500',
            'tipo'             => 'required|in:inicio_clases,fin_clases,vacaciones,suspension,examen,consejo,evento,festivo',
            'fecha_inicio'     => 'required|date',
            'fecha_fin'        => 'nullable|date|after_or_equal:fecha_inicio',
            'todo_el_dia'      => 'boolean',
            'color'            => 'nullable|regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
        ]);

        $evento = CalendarioEscolar::create($data);
        $this->audit->log(modulo: 'comunicacion', accion: 'create', model: CalendarioEscolar::class, modelId: $evento->id,
            descripcion: "Evento calendario: {$evento->titulo}");
        return redirect()->route('calendario.index')->with('success', 'Evento agregado al calendario.');
    }

    public function show(CalendarioEscolar $calendario): View
    {
        return view('calendario.show', compact('calendario'));
    }

    public function edit(CalendarioEscolar $calendario): View
    {
        $orgId  = auth()->user()->organizacion_id;
        $sedes  = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        $ciclos = CicloEscolar::where('organizacion_id', $orgId)->activo()->get();
        return view('calendario.edit', compact('calendario', 'sedes', 'ciclos'));
    }

    public function update(Request $request, CalendarioEscolar $calendario): RedirectResponse
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:500',
            'tipo'        => 'required|in:inicio_clases,fin_clases,vacaciones,suspension,examen,consejo,evento,festivo',
            'fecha_inicio'=> 'required|date',
            'fecha_fin'   => 'nullable|date|after_or_equal:fecha_inicio',
            'color'       => 'nullable|regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/',
        ]);
        $before = $calendario->toArray();
        $calendario->update($data);
        $this->audit->log(modulo: 'comunicacion', accion: 'update', model: CalendarioEscolar::class, modelId: $calendario->id,
            before: $before, after: $calendario->fresh()->toArray());
        return redirect()->route('calendario.index')->with('success', 'Evento actualizado.');
    }

    public function destroy(CalendarioEscolar $calendario): RedirectResponse
    {
        $calendario->delete();
        $this->audit->log(modulo: 'comunicacion', accion: 'delete', model: CalendarioEscolar::class, modelId: $calendario->id);
        return redirect()->route('calendario.index')->with('success', 'Evento eliminado.');
    }
}
