<x-layouts.app page-title="Cargo #{{ $cargo->id }}">
<x-ui.page-header title="Cargo #{{ $cargo->id }}"
    :items="[['label'=>'Cargos','url'=>route('finanzas.cargos.index')],['label'=>'Detalle']]">
    <x-slot name="actions">
        @if(in_array($cargo->estado,['pendiente','vencido']))
        <a href="{{ route('finanzas.pagos.create',['alumno_id'=>$cargo->alumno_id]) }}" class="btn btn-sm btn-success">Cobrar</a>
        @can('pagos.cancelar')
        <x-ui.confirm id="modal-cancel-cargo"
            title="¿Cancelar cargo?"
            :action="route('finanzas.cargos.destroy',$cargo)"
            method="DELETE" label="Cancelar" type="danger" :motivo="false">
            <x-slot name="trigger">
                <button type="button" class="btn btn-sm btn-outline-danger ms-1"
                    data-bs-toggle="modal" data-bs-target="#modal-cancel-cargo">Cancelar</button>
            </x-slot>
        </x-ui.confirm>
        @endcan
        @endif
    </x-slot>
</x-ui.page-header>

<div class="row g-3">
    <div class="col-md-5">
        <x-ui.card title="Información">
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-5 text-muted">Alumno</dt>
                <dd class="col-7"><a href="{{ route('alumnos.show',$cargo->alumno_id) }}" class="fw-medium text-decoration-none">{{ $cargo->alumno?->nombre_completo }}</a></dd>
                <dt class="col-5 text-muted">Concepto</dt><dd class="col-7">{{ $cargo->concepto?->nombre }}</dd>
                <dt class="col-5 text-muted">Importe</dt><dd class="col-7">${{ number_format($cargo->importe,2) }}</dd>
                <dt class="col-5 text-muted">Descuento</dt><dd class="col-7">${{ number_format($cargo->descuento,2) }}</dd>
                <dt class="col-5 text-muted">Recargo</dt><dd class="col-7">${{ number_format($cargo->recargo,2) }}</dd>
                <dt class="col-5 text-muted fw-bold">Total</dt>
                <dd class="col-7 fw-bold text-success">${{ number_format($cargo->total,2) }}</dd>
                <dt class="col-5 text-muted">Vencimiento</dt><dd class="col-7">{{ $cargo->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</dd>
                <dt class="col-5 text-muted">Estado</dt>
                <dd class="col-7">
                    <x-ui.badge :type="match($cargo->estado){'pendiente'=>'warning','pagado'=>'success','parcial'=>'info','cancelado'=>'secondary','vencido'=>'danger',default=>'secondary'}">
                        {{ ucfirst($cargo->estado) }}
                    </x-ui.badge>
                </dd>
            </dl>
        </x-ui.card>
    </div>
    <div class="col-md-7">
        <x-ui.card title="Parcialidades" :flush="true">
            <div class="table-responsive">
                <table class="table table-se table-sm mb-0">
                    <thead><tr><th>#</th><th>Vencimiento</th><th>Importe</th><th>Estado</th></tr></thead>
                    <tbody>
                        @forelse($cargo->parcialidades as $p)
                        <tr>
                            <td>{{ $p->numero }}</td>
                            <td style="font-size:.8rem">{{ $p->fecha_vencimiento?->format('d/m/Y') }}</td>
                            <td>${{ number_format($p->importe,2) }}</td>
                            <td><x-ui.badge :type="$p->estado==='pagado'?'success':($p->estado==='vencido'?'danger':'warning')" small>{{ ucfirst($p->estado) }}</x-ui.badge></td>
                        </tr>
                        @empty
                        <tr><td colspan="4"><x-ui.empty-state message="Sin parcialidades." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
        <x-ui.card title="Pagos aplicados" :flush="true" class="mt-3">
            <div class="table-responsive">
                <table class="table table-se table-sm mb-0">
                    <thead><tr><th>Fecha</th><th>Pago #</th><th>Aplicado</th><th>Método</th></tr></thead>
                    <tbody>
                        @forelse($cargo->pagoDetalles as $d)
                        <tr>
                            <td style="font-size:.8rem">{{ $d->pago?->fecha_pago?->format('d/m/Y') }}</td>
                            <td><a href="{{ route('finanzas.pagos.show',$d->pago_id) }}">#{{ $d->pago_id }}</a></td>
                            <td class="fw-semibold text-success">${{ number_format($d->importe_aplicado,2) }}</td>
                            <td style="font-size:.8rem">{{ $d->pago?->metodoPago?->nombre ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4"><x-ui.empty-state message="Sin pagos aplicados." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
