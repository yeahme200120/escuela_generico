<?php

namespace App\Http\Controllers\Finanzas;

use App\Http\Controllers\Controller;
use App\Models\Cargo;
use App\Models\ConceptoPago;
use App\Services\Auditoria\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CargoController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('pagos.ver');

        $orgId = auth()->user()->organizacion_id;

        $cargos = Cargo::query()
            ->with(['alumno', 'concepto', 'sede'])
            ->whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))
            ->when($request->alumno_id, fn($q, $id) => $q->where('alumno_id', $id))
            ->when($request->estado, fn($q, $estado) => $q->where('estado', $estado))
            ->when($request->sede_id, fn($q, $id) => $q->where('sede_id', $id))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('finanzas.cargos.index', compact('cargos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('pagos.registrar');

        $data = $request->validate([
            'alumno_id'         => ['required', 'integer', 'exists:alumnos,id'],
            'concepto_id'       => ['required', 'integer', 'exists:conceptos_pago,id'],
            'ciclo_escolar_id'  => ['required', 'integer', 'exists:ciclos_escolares,id'],
            'sede_id'           => ['required', 'integer', 'exists:sedes,id'],
            'importe'           => ['required', 'numeric', 'min:0'],
            'descuento'         => ['nullable', 'numeric', 'min:0'],
            'recargo'           => ['nullable', 'numeric', 'min:0'],
            'referencia'        => ['nullable', 'string', 'max:100'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ]);

        $importe   = (float) $data['importe'];
        $descuento = (float) ($data['descuento'] ?? 0);
        $recargo   = (float) ($data['recargo'] ?? 0);
        $total     = $importe - $descuento + $recargo;

        $cargo = Cargo::create([
            'alumno_id'         => $data['alumno_id'],
            'concepto_id'       => $data['concepto_id'],
            'ciclo_escolar_id'  => $data['ciclo_escolar_id'],
            'sede_id'           => $data['sede_id'],
            'referencia'        => $data['referencia'] ?? null,
            'importe'           => $importe,
            'descuento'         => $descuento,
            'recargo'           => $recargo,
            'total'             => $total,
            'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
            'estado'            => 'pendiente',
            'created_by'        => auth()->id(),
        ]);

        $this->audit->log(
            modulo:      'finanzas',
            accion:      'create',
            descripcion: "Cargo creado #{$cargo->id} para alumno {$cargo->alumno_id} — total \${$cargo->total}",
            model:       Cargo::class,
            modelId:     $cargo->id,
        );

        return redirect()->route('finanzas.cargos.index')
            ->with('success', 'Cargo registrado correctamente.');
    }
}
