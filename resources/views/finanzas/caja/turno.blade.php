<x-layouts.app page-title="Turno de caja #{{ $turno->id }}">
<x-ui.page-header title="Turno #{{ $turno->id }} — {{ $turno->caja?->nombre }}"
    :items="[['label'=>'Caja','url'=>route('finanzas.caja.index')],['label'=>'Turno #'.$turno->id]]">
    <x-slot name="actions">
        @if($turno->estaAbierto())
        {{-- Agregar movimiento --}}
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-movimiento">
            + Movimiento
        </button>
        @can('caja.cerrar')
        <button type="button" class="btn btn-sm btn-outline-danger ms-1" data-bs-toggle="modal" data-bs-target="#modal-cierre">
            Cerrar turno
        </button>
        @endcan
        @endif
    </x-slot>
</x-ui.page-header>

{{-- Resumen del turno --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <x-ui.stat-card label="Apertura" :value="'$'.number_format($turno->monto_apertura,2)" color="secondary" />
    </div>
    <div class="col-6 col-md-3">
        <x-ui.stat-card label="Ingresos" :value="'$'.number_format($totalIngresos,2)" color="success" />
    </div>
    <div class="col-6 col-md-3">
        <x-ui.stat-card label="Egresos/Retiros" :value="'$'.number_format($totalEgresos,2)" color="danger" />
    </div>
    <div class="col-6 col-md-3">
        <x-ui.stat-card label="Total en caja"
            :value="'$'.number_format($turno->monto_apertura + $totalIngresos - $totalEgresos, 2)"
            color="primary" />
    </div>
</div>

<x-ui.card title="Movimientos del turno" :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Hora</th><th>Tipo</th><th>Concepto</th><th>Importe</th><th>Cajero</th></tr></thead>
            <tbody>
                @forelse($turno->movimientos->sortByDesc('created_at') as $m)
                <tr>
                    <td style="font-size:.78rem">{{ $m->created_at?->format('H:i:s') }}</td>
                    <td>
                        <x-ui.badge :type="match($m->tipo){'ingreso'=>'success','egreso'=>'danger','retiro'=>'warning','devolucion'=>'info','ajuste'=>'secondary',default=>'secondary'}" small>
                            {{ ucfirst($m->tipo) }}
                        </x-ui.badge>
                    </td>
                    <td style="font-size:.875rem">{{ $m->concepto }}</td>
                    <td class="fw-semibold {{ in_array($m->tipo,['ingreso','devolucion'])?'text-success':'text-danger' }}">
                        {{ in_array($m->tipo,['ingreso','devolucion'])?'+':'-' }}${{ number_format($m->importe,2) }}
                    </td>
                    <td style="font-size:.8rem">{{ $m->usuario?->nombres ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5"><x-ui.empty-state message="Sin movimientos en este turno." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-ui.card>

{{-- Modal nuevo movimiento --}}
@if($turno->estaAbierto())
<x-ui.modal id="modal-movimiento" title="Registrar movimiento">
    <form method="POST" action="{{ route('finanzas.caja.movimiento',$turno) }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-medium">Tipo <span class="text-danger">*</span></label>
            <select name="tipo" class="form-select" required>
                @foreach(['ingreso'=>'Ingreso','egreso'=>'Egreso','retiro'=>'Retiro','devolucion'=>'Devolución','ajuste'=>'Ajuste'] as $v=>$l)
                <option value="{{ $v }}">{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium">Concepto <span class="text-danger">*</span></label>
            <input type="text" name="concepto" class="form-control" required placeholder="Descripción del movimiento">
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium">Importe <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="importe" class="form-control" step="0.01" min="0.01" required>
            </div>
        </div>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary btn-sm">Registrar</button>
        </x-slot>
    </form>
</x-ui.modal>

{{-- Modal cierre --}}
@can('caja.cerrar')
<x-ui.modal id="modal-cierre" title="Cerrar turno de caja">
    <form method="POST" action="{{ route('finanzas.caja.cerrar',$turno) }}">
        @csrf
        <div class="alert alert-info py-2 mb-3" style="font-size:.875rem">
            <strong>Total esperado:</strong>
            ${{ number_format($turno->monto_apertura + $totalIngresos - $totalEgresos, 2) }}
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium">Monto físico en caja <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="monto_cierre" class="form-control" step="0.01" min="0" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium">Observaciones</label>
            <textarea name="observaciones" class="form-control" rows="2"></textarea>
        </div>
        <x-slot name="footer">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger btn-sm">Confirmar cierre</button>
        </x-slot>
    </form>
</x-ui.modal>
@endcan
@endif
</x-layouts.app>
