<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finanzas\PagoRequest;
use App\Models\Alumno;
use App\Models\Cargo;
use App\Models\MetodoPago;
use App\Models\Pago;
use App\Models\Sede;
use App\Services\Finanzas\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function __construct(private readonly PagoService $pagoService) {}

    public function index(Request $request): View
    {
        $this->authorize('pagos.ver');
        $orgId = auth()->user()->organizacion_id;

        $pagos = Pago::with(['alumno', 'sede', 'metodoPago', 'usuario'])
            ->whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->alumno_id, fn($q, $id) => $q->where('alumno_id', $id))
            ->when($request->sede_id,   fn($q, $id) => $q->where('sede_id', $id))
            ->when($request->fecha,     fn($q, $f)  => $q->whereDate('fecha_pago', $f))
            ->when($request->estado,    fn($q, $e)  => $q->where('estado', $e))
            ->orderByDesc('fecha_pago')->orderByDesc('created_at')
            ->paginate(50)->withQueryString();

        return view('finanzas.pagos.index', compact('pagos'));
    }

    public function create(Request $request): View
    {
        $this->authorize('pagos.registrar');
        $orgId    = auth()->user()->organizacion_id;
        $alumnos  = Alumno::where('organizacion_id', $orgId)->activos()->orderBy('apellido_paterno')->get();
        $metodos  = MetodoPago::activos()->get();
        $sedes    = Sede::whereHas('organizacion', fn($q) => $q->where('id', $orgId))->activas()->get();
        $alumnoSeleccionado = $request->alumno_id ? Alumno::with('cargos')->find($request->alumno_id) : null;
        $cargos   = $alumnoSeleccionado
            ? Cargo::where('alumno_id', $alumnoSeleccionado->id)->whereIn('estado', ['pendiente','parcial','vencido'])->get()
            : collect();
        return view('finanzas.pagos.create', compact('alumnos', 'metodos', 'sedes', 'alumnoSeleccionado', 'cargos'));
    }

    public function store(PagoRequest $request): RedirectResponse
    {
        $pago = $this->pagoService->registrar($request->validated(), auth()->id());
        return redirect()->route('finanzas.pagos.index')
            ->with('success', "Pago #{$pago->id} de \${$pago->importe} registrado.");
    }

    public function show(Pago $pago): View
    {
        $this->authorize('pagos.ver');
        $pago->load('alumno', 'sede', 'metodoPago', 'usuario', 'pagoDetalles.cargo.concepto');
        return view('finanzas.pagos.show', compact('pago'));
    }

    public function destroy(Request $request, Pago $pago): RedirectResponse
    {
        $this->authorize('pagos.cancelar');
        $request->validate(['motivo' => 'required|string|min:10']);
        $this->pagoService->cancelar($pago, $request->motivo, auth()->id());
        return redirect()->route('finanzas.pagos.index')
            ->with('success', "Pago #{$pago->id} cancelado.");
    }
}
