<x-layouts.app page-title="Docentes">
<x-ui.page-header title="Docentes" subtitle="Gestión de docentes, asignaciones y estadísticas.">
    <x-slot name="actions">
        @can('docentes.crear')
        <a href="{{ route('docentes.create') }}" class="btn btn-primary btn-sm">+ Nuevo docente</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="route('docentes.index')">
    <x-slot name="fields">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Nombre, email..." value="{{ request('q') }}">
        </div>
        <div class="col-md-2">
            <select name="estatus" class="form-select form-select-sm">
                <option value="">Estatus</option>
                <option value="activo"   {{ request('estatus')==='activo'   ?'selected':'' }}>Activo</option>
                <option value="inactivo" {{ request('estatus')==='inactivo' ?'selected':'' }}>Inactivo</option>
                <option value="baja"     {{ request('estatus')==='baja'     ?'selected':'' }}>Baja</option>
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr><th>Docente</th><th>Especialidad</th><th>Contrato</th><th>Nº empleado</th><th>Estatus</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($docentes as $d)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <x-ui.avatar :name="$d->user?->nombre_completo" size="sm" />
                            <div>
                                <div class="fw-medium" style="font-size:.875rem">{{ $d->user?->nombre_completo }}</div>
                                <small class="text-muted">{{ $d->user?->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.875rem">{{ $d->especialidad ?? '—' }}</td>
                    <td><x-ui.badge type="info" small>{{ ucfirst($d->tipo_contrato) }}</x-ui.badge></td>
                    <td style="font-size:.8rem">{{ $d->numero_empleado ?? '—' }}</td>
                    <td>
                        <x-ui.badge :type="match($d->estatus){'activo'=>'success','baja'=>'danger',default=>'secondary'}">
                            {{ ucfirst($d->estatus) }}
                        </x-ui.badge>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('docentes.show',$d) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @can('docentes.editar')
                        <a href="{{ route('docentes.edit',$d) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin docentes registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top d-flex justify-content-between">
        <small class="text-muted">{{ $docentes->total() }} docentes</small>
        {{ $docentes->links() }}
    </div>
</x-ui.card>
</x-layouts.app>
