<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Services\Finanzas\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PagoController extends Controller
{
    public function __construct(
        private readonly PagoService $pagoService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('pagos.ver');

        $orgId = auth()->user()->organizacion_id;

        $pagos = Pago::query()
            ->with(['alumno', 'sede', 'metodoPago', 'usuario'])
            ->whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->alumno_id, fn($q, $id) => $q->where('alumno_id', $id))
            ->when($request->sede_id, fn($q, $id) => $q->where('sede_id', $id))
            ->when($request->fecha, fn($q, $fecha) => $q->where('fecha_pago', $fecha))
            ->when($request->estado, fn($q, $estado) => $q->where('estado', $estado))
            ->orderByDesc('fecha_pago')
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('finanzas.pagos.index', compact('pagos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('pagos.registrar');

        $data = $request->validate([
            'alumno_id'      => ['required', 'integer', 'exists:alumnos,id'],
            'sede_id'        => ['required', 'integer', 'exists:sedes,id'],
            'importe'        => ['required', 'numeric', 'min:0.01'],
            'fecha_pago'     => ['required', 'date'],
            'metodo_pago_id' => ['required', 'integer', 'exists:metodos_pago,id'],
            'cargos'         => ['required', 'array', 'min:1'],
            'cargos.*.cargo_id'         => ['required', 'integer', 'exists:cargos,id'],
            'cargos.*.importe_aplicado' => ['required', 'numeric', 'min:0.01'],
            'cargos.*.parcialidad_id'   => ['nullable', 'integer', 'exists:parcialidades,id'],
        ]);

        $pago = $this->pagoService->registrar($data, auth()->id());

        return redirect()->route('finanzas.pagos.index')
            ->with('success', "Pago #{$pago->id} registrado correctamente.");
    }

    public function destroy(Request $request, Pago $pago): RedirectResponse
    {
        $this->authorize('pagos.cancelar');

        $request->validate([
            'motivo' => ['required', 'string', 'min:10'],
        ]);

        $this->pagoService->cancelar($pago, $request->motivo, auth()->id());

        return redirect()->route('finanzas.pagos.index')
            ->with('success', "Pago #{$pago->id} cancelado.");
    }
}
