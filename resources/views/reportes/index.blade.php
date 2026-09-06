<x-layouts.app page-title="Reportes">
<x-ui.page-header title="Reportes y exportaciones" subtitle="Genera reportes CSV o encola reportes masivos via Python.">
    <x-slot name="actions">
        @can('reportes.ver')
        <a href="{{ route('reportes.create') }}" class="btn btn-primary btn-sm">+ Nuevo reporte</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Tipo</th><th>Solicitado por</th><th>Estado</th><th>Progreso</th><th>Creado</th><th></th></tr></thead>
            <tbody>
                @forelse($reportes as $r)
                <tr>
                    <td>
                        <x-ui.badge type="info" small>{{ ucfirst($r->tipo) }}</x-ui.badge>
                        <div style="font-size:.78rem" class="text-muted mt-1">{{ $r->job_id }}</div>
                    </td>
                    <td style="font-size:.8rem">{{ $r->usuario?->nombres ?? '—' }}</td>
                    <td>
                        <x-ui.badge :type="match($r->estado){'completado'=>'success','fallido'=>'danger','procesando'=>'info',default=>'warning'}">
                            {{ ucfirst($r->estado) }}
                        </x-ui.badge>
                    </td>
                    <td>
                        <div class="progress" style="height:6px;width:80px">
                            <div class="progress-bar" style="width:{{ $r->progreso }}%"></div>
                        </div>
                        <small class="text-muted">{{ $r->progreso }}%</small>
                    </td>
                    <td style="font-size:.78rem">{{ $r->created_at?->diffForHumans() }}</td>
                    <td class="text-end">
                        @if($r->tieneArchivo())
                        <a href="{{ route('reportes.descargar',$r) }}" class="btn btn-sm btn-outline-success">⬇ Descargar</a>
                        @elseif($r->estado === 'completado')
                        <a href="{{ route('reportes.show',$r) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin reportes generados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $reportes->links() }}</div>
</x-ui.card>
</x-layouts.app>
