<x-layouts.app page-title="Sedes">
<x-ui.page-header title="Sedes">
    <x-slot name="actions">
        @can('sedes.crear')
        <a href="{{ route('sedes.create') }}" class="btn btn-primary btn-sm">+ Nueva sede</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Sede</th><th>Ciudad</th><th>Geocerca</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                @forelse($sedes as $s)
                <tr>
                    <td>
                        <div class="fw-medium">{{ $s->nombre }}</div>
                        <small class="text-muted">{{ $s->escuela?->nombre }}</small>
                    </td>
                    <td style="font-size:.8rem">{{ $s->ciudad }}</td>
                    <td>
                        @if($s->geocerca_activa)
                            <x-ui.badge type="success" small>Activa ±{{ $s->radio_geocerca_metros }}m</x-ui.badge>
                        @else
                            <x-ui.badge type="secondary" small>Inactiva</x-ui.badge>
                        @endif
                    </td>
                    <td><x-ui.badge :type="$s->activa?'success':'secondary'">{{ $s->activa?'Activa':'Inactiva' }}</x-ui.badge></td>
                    <td class="text-end">
                        <a href="{{ route('sedes.show',$s) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @can('sedes.editar')
                        <a href="{{ route('sedes.edit',$s) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="5"><x-ui.empty-state message="Sin sedes registradas." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top d-flex justify-content-between">
        <small class="text-muted">{{ $sedes->total() }} sedes</small>
        {{ $sedes->links() }}
    </div>
</x-ui.card>
</x-layouts.app>
