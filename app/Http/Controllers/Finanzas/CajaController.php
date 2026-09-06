<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\MovimientoCaja;
use App\Models\TurnoCaja;
use App\Services\Finanzas\CajaService;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CajaController extends Controller
{
    public function __construct(
        private readonly CajaService  $cajaService,
        private readonly AuditService $audit,
    ) {}

    public function index(): View
    {
        $this->authorize('caja.ver');
        $orgId = auth()->user()->organizacion_id;

        $cajas = Caja::with(['sede', 'turnos' => fn($q) => $q->where('estado', 'abierto')->with('usuario')])
            ->whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->activas()->orderBy('nombre')->get();

        return view('finanzas.caja.index', compact('cajas'));
    }

    public function abrir(Request $request, Caja $caja): RedirectResponse
    {
        $this->authorize('caja.abrir');
        $request->validate(['monto_apertura' => 'required|numeric|min:0']);

        $this->cajaService->abrir($caja->id, auth()->id(), (float) $request->monto_apertura);

        return redirect()->route('finanzas.caja.index')
            ->with('success', "Caja '{$caja->nombre}' abierta.");
    }

    public function cerrar(Request $request, TurnoCaja $turno): RedirectResponse
    {
        $this->authorize('caja.cerrar');
        $request->validate([
            'monto_cierre'  => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $this->cajaService->cerrar($turno, (float) $request->monto_cierre, $request->observaciones ?? '');

        return redirect()->route('finanzas.caja.index')
            ->with('success', "Turno #{$turno->id} cerrado.");
    }

    public function movimiento(Request $request, TurnoCaja $turno): RedirectResponse
    {
        $this->authorize('caja.ver');
        $request->validate([
            'tipo'     => 'required|in:ingreso,egreso,retiro,devolucion,ajuste',
            'concepto' => 'required|string|max:300',
            'importe'  => 'required|numeric|min:0.01',
        ]);

        $this->cajaService->registrarMovimiento(
            $turno, $request->tipo, $request->concepto, (float) $request->importe, null, auth()->id()
        );

        return back()->with('success', 'Movimiento registrado.');
    }

    public function turno(TurnoCaja $turno): View
    {
        $this->authorize('caja.ver');
        $turno->load(['caja', 'usuario', 'movimientos.usuario']);
        $totalIngresos = $turno->movimientos()->where('tipo', 'ingreso')->sum('importe');
        $totalEgresos  = $turno->movimientos()->whereIn('tipo', ['egreso','retiro'])->sum('importe');
        return view('finanzas.caja.turno', compact('turno', 'totalIngresos', 'totalEgresos'));
    }
}
