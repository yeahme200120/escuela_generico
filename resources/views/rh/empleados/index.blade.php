<x-layouts.app page-title="Empleados">
<x-ui.page-header title="Empleados" subtitle="Gestión de recursos humanos.">
    <x-slot name="actions">
        @can('rh.gestionar')
        <a href="{{ route('rh.empleados.create') }}" class="btn btn-primary btn-sm">+ Nuevo empleado</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<x-ui.filter-bar :action="route('rh.empleados.index')">
    <x-slot name="fields">
        <div class="col-md-4"><input type="text" name="q" class="form-control form-control-sm" placeholder="Nombre o email..." value="{{ request('q') }}"></div>
        <div class="col-md-2">
            <select name="estatus" class="form-select form-select-sm">
                <option value="">Estatus</option>
                <option value="activo"     {{ request('estatus')==='activo'    ?'selected':'' }}>Activo</option>
                <option value="baja"       {{ request('estatus')==='baja'      ?'selected':'' }}>Baja</option>
                <option value="suspendido" {{ request('estatus')==='suspendido'?'selected':'' }}>Suspendido</option>
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Empleado</th><th>Puesto</th><th>Departamento</th><th>Contrato</th><th>Salario</th><th>Estatus</th><th></th></tr></thead>
            <tbody>
                @forelse($empleados as $e)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <x-ui.avatar :name="$e->user?->nombre_completo" size="sm" />
                            <div>
                                <div class="fw-medium" style="font-size:.875rem">{{ $e->user?->nombre_completo }}</div>
                                <small class="text-muted">{{ $e->numero_empleado ?? '—' }}</small>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.875rem">{{ $e->puesto ?? '—' }}</td>
                    <td style="font-size:.8rem">{{ $e->departamento ?? '—' }}</td>
                    <td><x-ui.badge type="info" small>{{ ucfirst($e->tipo_contrato) }}</x-ui.badge></td>
                    <td style="font-size:.875rem">${{ $e->salario ? number_format($e->salario,2) : '—' }}</td>
                    <td><x-ui.badge :type="match($e->estatus){'activo'=>'success','baja'=>'danger',default=>'warning'}">{{ ucfirst($e->estatus) }}</x-ui.badge></td>
                    <td class="text-end">
                        <a href="{{ route('rh.empleados.show',$e) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @can('rh.gestionar')
                        <a href="{{ route('rh.empleados.edit',$e) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin empleados registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top d-flex justify-content-between">
        <small class="text-muted">{{ $empleados->total() }} empleados</small>
        {{ $empleados->links() }}
    </div>
</x-ui.card>
</x-layouts.app>
