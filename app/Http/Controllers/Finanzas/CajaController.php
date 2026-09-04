<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\TurnoCaja;
use App\Services\Finanzas\CajaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CajaController extends Controller
{
    public function __construct(
        private readonly CajaService $cajaService,
    ) {}

    public function index(): View
    {
        $this->authorize('caja.ver');

        $orgId = auth()->user()->organizacion_id;

        $cajas = Caja::query()
            ->with(['sede', 'turnos' => fn($q) => $q->where('estado', 'abierto')->latest()])
            ->whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->activas()
            ->orderBy('nombre')
            ->get();

        return view('finanzas.caja.index', compact('cajas'));
    }

    public function abrir(Request $request, Caja $caja): RedirectResponse
    {
        $this->authorize('caja.abrir');

        $request->validate([
            'monto_apertura' => ['required', 'numeric', 'min:0'],
        ]);

        $this->cajaService->abrir(
            cajaId:         $caja->id,
            userId:         auth()->id(),
            montoApertura:  (float) $request->monto_apertura,
        );

        return redirect()->route('finanzas.caja.index')
            ->with('success', "Caja \"{$caja->nombre}\" abierta correctamente.");
    }

    public function cerrar(Request $request, TurnoCaja $turno): RedirectResponse
    {
        $this->authorize('caja.cerrar');

        $request->validate([
            'monto_cierre'  => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->cajaService->cerrar(
            turno:         $turno,
            montoCierre:   (float) $request->monto_cierre,
            observaciones: $request->observaciones ?? '',
        );

        return redirect()->route('finanzas.caja.index')
            ->with('success', "Turno de caja #{$turno->id} cerrado correctamente.");
    }
}
