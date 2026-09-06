<x-layouts.app page-title="Justificantes">
<x-ui.page-header title="Justificantes de asistencia" subtitle="Solicitudes pendientes de autorización §40">
    <x-slot name="actions">
        <a href="{{ route('asistencias.index') }}" class="btn btn-sm btn-outline-secondary">Pase de lista</a>
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="request()->url()">
    <x-slot name="fields">
        <div class="col-md-3"><input type="text" name="q" class="form-control form-control-sm" placeholder="Alumno..." value="{{ request('q') }}"></div>
        <div class="col-md-2">
            <select name="estado" class="form-select form-select-sm">
                <option value="">Estado</option>
                <option value="pendiente" {{ request('estado')==='pendiente'?'selected':'' }}>Pendiente</option>
                <option value="aprobado"  {{ request('estado')==='aprobado' ?'selected':'' }}>Aprobado</option>
                <option value="rechazado" {{ request('estado')==='rechazado'?'selected':'' }}>Rechazado</option>
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr><th>Alumno</th><th>Período</th><th>Motivo</th><th>Documento</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($justificantes as $j)
                <tr>
                    <td>
                        <div class="fw-medium" style="font-size:.875rem">{{ $j->alumno?->nombre_completo }}</div>
                        <small class="text-muted">{{ $j->alumno?->matricula }}</small>
                    </td>
                    <td style="font-size:.8rem">
                        {{ $j->fecha_inicio?->format('d/m/Y') }} — {{ $j->fecha_fin?->format('d/m/Y') }}
                    </td>
                    <td style="font-size:.875rem;max-width:200px" class="text-truncate">{{ $j->motivo }}</td>
                    <td>
                        @if($j->documento)
                        <a href="{{ Storage::url($j->documento) }}" target="_blank" class="btn btn-link btn-sm p-0">Ver</a>
                        @else
                        <span class="text-muted" style="font-size:.8rem">—</span>
                        @endif
                    </td>
                    <td>
                        <x-ui.badge :type="match($j->estado){'aprobado'=>'success','rechazado'=>'danger','pendiente'=>'warning',default=>'secondary'}">
                            {{ ucfirst($j->estado) }}
                        </x-ui.badge>
                    </td>
                    <td class="text-end">
                        @if($j->estado === 'pendiente')
                        @can('asistencias.editar')
                        <form method="POST" action="{{ route('asistencias.justificante.aprobar',$j) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success">Aprobar</button>
                        </form>
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin justificantes con los filtros aplicados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $justificantes->links() }}</div>
</x-ui.card>
</x-layouts.app>
