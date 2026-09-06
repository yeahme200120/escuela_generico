<x-layouts.app page-title="Empleado: {{ $empleado->user?->nombre_completo }}">
<x-ui.page-header title="{{ $empleado->user?->nombre_completo }}"
    :items="[['label'=>'Empleados','url'=>route('rh.empleados.index')],['label'=>'Ficha']]">
    <x-slot name="actions">
        @can('rh.gestionar')
        <a href="{{ route('rh.empleados.edit',$empleado) }}" class="btn btn-sm btn-outline-primary">Editar</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<div class="row g-3">
    <div class="col-md-4">
        <x-ui.card title="Datos del empleado">
            <div class="text-center mb-3">
                <x-ui.avatar :name="$empleado->user?->nombre_completo" size="xl" />
                <div class="fw-semibold mt-2">{{ $empleado->user?->nombre_completo }}</div>
                <div class="text-muted" style="font-size:.875rem">{{ $empleado->user?->email }}</div>
                <x-ui.badge :type="match($empleado->estatus){'activo'=>'success','baja'=>'danger',default=>'warning'}" class="mt-1">
                    {{ ucfirst($empleado->estatus) }}
                </x-ui.badge>
            </div>
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-5 text-muted">Nº empleado</dt><dd class="col-7">{{ $empleado->numero_empleado ?? '—' }}</dd>
                <dt class="col-5 text-muted">Puesto</dt><dd class="col-7">{{ $empleado->puesto ?? '—' }}</dd>
                <dt class="col-5 text-muted">Departamento</dt><dd class="col-7">{{ $empleado->departamento ?? '—' }}</dd>
                <dt class="col-5 text-muted">Contrato</dt><dd class="col-7">{{ ucfirst($empleado->tipo_contrato) }}</dd>
                <dt class="col-5 text-muted">Salario</dt><dd class="col-7">${{ $empleado->salario ? number_format($empleado->salario,2) : '—' }}</dd>
                <dt class="col-5 text-muted">Ingreso</dt><dd class="col-7">{{ $empleado->fecha_ingreso?->format('d/m/Y') ?? '—' }}</dd>
            </dl>
        </x-ui.card>
    </div>
    <div class="col-md-8">
        <x-ui.card title="Contratos" :flush="true">
            <div class="table-responsive">
                <table class="table table-se table-sm mb-0">
                    <thead><tr><th>Tipo</th><th>Inicio</th><th>Fin</th><th>Salario</th><th>Estado</th></tr></thead>
                    <tbody>
                        @forelse($empleado->contratos as $c)
                        <tr>
                            <td style="font-size:.8rem">{{ ucfirst($c->tipo) }}</td>
                            <td style="font-size:.8rem">{{ $c->fecha_inicio?->format('d/m/Y') }}</td>
                            <td style="font-size:.8rem">{{ $c->fecha_fin?->format('d/m/Y') ?? 'Indefinido' }}</td>
                            <td style="font-size:.8rem">${{ $c->salario ? number_format($c->salario,2) : '—' }}</td>
                            <td><x-ui.badge :type="$c->activo?'success':'secondary'" small>{{ $c->activo?'Activo':'Inactivo' }}</x-ui.badge></td>
                        </tr>
                        @empty
                        <tr><td colspan="5"><x-ui.empty-state message="Sin contratos." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
