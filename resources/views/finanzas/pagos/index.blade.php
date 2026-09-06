<x-layouts.app page-title="Pagos">
<x-ui.page-header title="Registro de pagos">
    <x-slot name="actions">
        @can('pagos.registrar')
        <a href="{{ route('finanzas.pagos.create') }}" class="btn btn-primary btn-sm">+ Registrar pago</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="route('finanzas.pagos.index')">
    <x-slot name="fields">
        <div class="col-md-3">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Alumno o referencia..." value="{{ request('q') }}">
        </div>
        <div class="col-md-2">
            <input type="date" name="fecha" class="form-control form-control-sm" value="{{ request('fecha') }}" placeholder="Fecha">
        </div>
        <div class="col-md-2">
            <select name="estado" class="form-select form-select-sm">
                <option value="">Estado</option>
                <option value="activo"    {{ request('estado')==='activo'   ?'selected':'' }}>Activo</option>
                <option value="cancelado" {{ request('estado')==='cancelado'?'selected':'' }}>Cancelado</option>
                <option value="devuelto"  {{ request('estado')==='devuelto' ?'selected':'' }}>Devuelto</option>
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr><th>Alumno</th><th>Importe</th><th>Método</th><th>Fecha</th><th>Cajero</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($pagos as $p)
                <tr class="{{ $p->estado==='cancelado'?'text-muted':'' }}">
                    <td style="font-size:.875rem">
                        <div class="fw-medium">{{ $p->alumno?->nombre_completo }}</div>
                        <small class="text-muted">{{ $p->alumno?->matricula }}</small>
                    </td>
                    <td class="fw-semibold {{ $p->estado==='cancelado'?'text-decoration-line-through':'' }}">
                        ${{ number_format($p->importe,2) }}
                    </td>
                    <td style="font-size:.8rem">{{ $p->metodoPago?->nombre ?? '—' }}</td>
                    <td style="font-size:.8rem">{{ $p->fecha_pago?->format('d/m/Y') }}</td>
                    <td style="font-size:.8rem">{{ $p->usuario?->nombres ?? '—' }}</td>
                    <td>
                        <x-ui.badge :type="match($p->estado){'activo'=>'success','cancelado'=>'secondary','devuelto'=>'warning',default=>'secondary'}">
                            {{ ucfirst($p->estado) }}
                        </x-ui.badge>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('finanzas.pagos.show',$p) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @if($p->estado === 'activo')
                        @can('pagos.cancelar')
                        <x-ui.confirm id="cancel-pago-{{ $p->id }}"
                            title="¿Cancelar pago #{{ $p->id }}?"
                            message="Se revertirán los cargos aplicados. Ingresa el motivo."
                            :action="route('finanzas.pagos.destroy',$p)"
                            method="DELETE" label="Cancelar pago" type="danger" :motivo="true">
                            <x-slot name="trigger">
                                <button type="button" class="btn btn-sm btn-outline-danger ms-1"
                                    data-bs-toggle="modal" data-bs-target="#cancel-pago-{{ $p->id }}">Cancelar</button>
                            </x-slot>
                        </x-ui.confirm>
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin pagos registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top d-flex justify-content-between">
        <small class="text-muted">{{ $pagos->total() }} registros</small>
        {{ $pagos->links() }}
    </div>
</x-ui.card>
</x-layouts.app>
