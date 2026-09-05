<x-layouts.app page-title="Ciclos Escolares">
<x-ui.page-header title="Ciclos Escolares" subtitle="Gestión de ciclos escolares por escuela.">
    <x-slot name="actions">
        @can('ciclos.crear')
        <a href="{{ route('ciclos.create') }}" class="btn btn-primary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16"><path d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2z"/></svg>
            Nuevo ciclo
        </a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr>
                    <th>Nombre</th><th>Escuela</th><th>Inicio</th><th>Fin</th>
                    <th>Estado</th><th>Actual</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($ciclos as $ciclo)
                <tr>
                    <td class="fw-medium">{{ $ciclo->nombre }}</td>
                    <td>{{ $ciclo->escuela->nombre }}</td>
                    <td>{{ $ciclo->fecha_inicio->format('d/m/Y') }}</td>
                    <td>{{ $ciclo->fecha_fin->format('d/m/Y') }}</td>
                    <td>
                        <x-ui.badge :type="match($ciclo->estatus){ 'activo'=>'success','cerrado'=>'secondary','configuracion'=>'warning', default=>'secondary' }">
                            {{ ucfirst($ciclo->estatus) }}
                        </x-ui.badge>
                    </td>
                    <td>
                        @if($ciclo->es_actual)
                            <x-ui.badge type="primary" pill>Actual</x-ui.badge>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('ciclos.show', $ciclo) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @can('ciclos.crear')
                        <a href="{{ route('ciclos.edit', $ciclo) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin ciclos registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $ciclos->links() }}</div>
</x-ui.card>
</x-layouts.app>
