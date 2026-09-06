<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finanzas\CargoRequest;
use App\Models\Alumno;
use App\Models\Cargo;
use App\Models\ConceptoPago;
use App\Models\CicloEscolar;
use App\Models\Sede;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CargoController extends Controller
{
    public function __construct(private readonly AuditService $audit) {}

    public function index(Request $request): View
    {
        $this->authorize('pagos.ver');
        $orgId  = auth()->user()->organizacion_id;

        $cargos = Cargo::with(['alumno', 'concepto', 'sede'])
            ->whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->alumno_id, fn($q, $id) => $q->where('alumno_id', $id))
            ->when($request->estado,    fn($q, $e)  => $q->where('estado', $e))
            ->when($request->sede_id,   fn($q, $id) => $q->where('sede_id', $id))
            ->when($request->q, fn($q, $s) => $q->whereHas('alumno', fn($a) =>
                $a->where('nombres', 'like', "%$s%")->orWhere('matricula', 'like', "%$s%")
            ))
            ->orderByDesc('created_at')->paginate(25)->withQueryString();

        return view('finanzas.cargos.index', compact('cargos'));
    }

    public function create(Request $request): View
    {
        $this->authorize('pagos.registrar');
        $orgId    = auth()->user()->organizacion_id;
        $alumnos  = Alumno::where('organizacion_id', $orgId)->activos()->orderBy('apellido_paterno')->get();
        $conceptos = ConceptoPago::where(fn($q) => $q->where('organizacion_id', $orgId)->orWhereNull('organizacion_id'))->activos()->get();
        $ciclos   = CicloEscolar::where('organizacion_id', $orgId)->activo()->get();
        $sedes    = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        $alumnoSeleccionado = $request->alumno_id ? Alumno::find($request->alumno_id) : null;
        return view('finanzas.cargos.create', compact('alumnos', 'conceptos', 'ciclos', 'sedes', 'alumnoSeleccionado'));
    }

    public function store(CargoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['total']      = ($data['importe'] - ($data['descuento'] ?? 0)) + ($data['recargo'] ?? 0);
        $data['created_by'] = auth()->id();

        $cargo = Cargo::create($data);
        $this->audit->log(modulo: 'finanzas', accion: 'create', model: Cargo::class, modelId: $cargo->id,
            descripcion: "Cargo creado \${$cargo->total} alumno#{$cargo->alumno_id}");

        return redirect()->route('finanzas.cargos.index')->with('success', "Cargo de \${$cargo->total} registrado.");
    }

    public function show(Cargo $cargo): View
    {
        $this->authorize('pagos.ver');
        $cargo->load('alumno', 'concepto', 'parcialidades', 'pagoDetalles.pago');
        return view('finanzas.cargos.show', compact('cargo'));
    }

    public function destroy(Cargo $cargo): RedirectResponse
    {
        $this->authorize('pagos.cancelar');
        if ($cargo->estado === 'pagado') return back()->with('error', 'No se puede eliminar un cargo pagado.');
        $before = $cargo->toArray();
        $cargo->update(['estado' => 'cancelado']);
        $this->audit->log(modulo: 'finanzas', accion: 'cancel', model: Cargo::class, modelId: $cargo->id,
            before: $before, after: ['estado' => 'cancelado']);
        return redirect()->route('finanzas.cargos.index')->with('success', 'Cargo cancelado.');
    }
}
