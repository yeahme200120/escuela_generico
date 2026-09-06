<x-layouts.app page-title="Pago #{{ $pago->id }}">
<x-ui.page-header title="Pago #{{ $pago->id }}"
    :items="[['label'=>'Pagos','url'=>route('finanzas.pagos.index')],['label'=>'#'.$pago->id]]">
    <x-slot name="actions">
        @if($pago->estado === 'activo')
        @can('pagos.cancelar')
        <x-ui.confirm id="modal-cancelar-pago"
            title="¿Cancelar pago #{{ $pago->id }}?"
            message="Se revertirán todos los cargos aplicados. Esta acción requiere motivo."
            :action="route('finanzas.pagos.destroy',$pago)"
            method="DELETE" label="Cancelar pago" type="danger" :motivo="true">
            <x-slot name="trigger">
                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modal-cancelar-pago">
                    Cancelar pago
                </button>
            </x-slot>
        </x-ui.confirm>
        @endcan
        @endif
    </x-slot>
</x-ui.page-header>

<div class="row g-3">
    <div class="col-md-5">
        <x-ui.card title="Datos del pago">
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-5 text-muted">Alumno</dt>
                <dd class="col-7"><a href="{{ route('alumnos.show',$pago->alumno_id) }}" class="fw-medium text-decoration-none">{{ $pago->alumno?->nombre_completo }}</a></dd>
                <dt class="col-5 text-muted">Matrícula</dt>
                <dd class="col-7">{{ $pago->alumno?->matricula }}</dd>
                <dt class="col-5 text-muted">Fecha pago</dt>
                <dd class="col-7">{{ $pago->fecha_pago?->format('d/m/Y') }}</dd>
                <dt class="col-5 text-muted">Método</dt>
                <dd class="col-7">{{ $pago->metodoPago?->nombre ?? '—' }}</dd>
                <dt class="col-5 text-muted">Referencia</dt>
                <dd class="col-7">{{ $pago->referencia ?? '—' }}</dd>
                <dt class="col-5 text-muted">Cajero</dt>
                <dd class="col-7">{{ $pago->usuario?->nombres ?? '—' }}</dd>
                <dt class="col-5 text-muted">Sede</dt>
                <dd class="col-7">{{ $pago->sede?->nombre }}</dd>
                <dt class="col-5 text-muted">Estado</dt>
                <dd class="col-7">
                    <x-ui.badge :type="match($pago->estado){'activo'=>'success','cancelado'=>'secondary','devuelto'=>'warning',default=>'secondary'}">
                        {{ ucfirst($pago->estado) }}
                    </x-ui.badge>
                </dd>
                <dt class="col-5 text-muted fw-bold">TOTAL</dt>
                <dd class="col-7 fw-bold text-success" style="font-size:1.1rem">${{ number_format($pago->importe,2) }}</dd>
            </dl>
            @if($pago->motivo_cancelacion)
            <div class="alert alert-danger mt-3 py-2 mb-0" style="font-size:.8rem">
                <strong>Motivo cancelación:</strong> {{ $pago->motivo_cancelacion }}
            </div>
            @endif
        </x-ui.card>
    </div>
    <div class="col-md-7">
        <x-ui.card title="Cargos aplicados" :flush="true">
            <div class="table-responsive">
                <table class="table table-se mb-0">
                    <thead><tr><th>Concepto</th><th>Cargo total</th><th>Aplicado</th></tr></thead>
                    <tbody>
                        @forelse($pago->pagoDetalles as $d)
                        <tr>
                            <td style="font-size:.875rem">{{ $d->cargo?->concepto?->nombre }}</td>
                            <td>${{ number_format($d->cargo?->total,2) }}</td>
                            <td class="fw-semibold text-success">${{ number_format($d->importe_aplicado,2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3"><x-ui.empty-state message="Sin detalles." /></td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="2" class="text-end">Total aplicado:</td>
                            <td class="text-success">${{ number_format($pago->importe,2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
