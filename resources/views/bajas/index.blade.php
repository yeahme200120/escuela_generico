<x-layouts.app page-title="Bajas">
<x-ui.page-header title="Registro de bajas" subtitle="Bajas temporales, definitivas, deserciones y traslados." />
<x-ui.filter-bar :action="route('bajas.index')">
    <x-slot name="fields">
        <div class="col-md-3"><input type="text" name="q" class="form-control form-control-sm" placeholder="Alumno..." value="{{ request('q') }}"></div>
        <div class="col-md-2">
            <select name="tipo" class="form-select form-select-sm">
                <option value="">Tipo</option>
                @foreach(['temporal','definitiva','desercion','traslado','egreso'] as $t)
                <option value="{{ $t }}" {{ request('tipo')===$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="estatus" class="form-select form-select-sm">
                <option value="">Estatus</option>
                @foreach(['solicitada','aprobada','activa','reingresado','cancelada'] as $e)
                <option value="{{ $e }}" {{ request('estatus')===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Alumno</th><th>Tipo</th><th>Motivo</th><th>Fecha</th><th>Estatus</th><th></th></tr></thead>
            <tbody>
                @forelse($items ?? [] as $b)
                <tr>
                    <td style="font-size:.875rem">
                        <div class="fw-medium">{{ $b->alumno?->nombre_completo }}</div>
                        <small class="text-muted">{{ $b->alumno?->matricula }}</small>
                    </td>
                    <td><x-ui.badge :type="match($b->tipo){'temporal'=>'warning','desercion'=>'danger',default=>'secondary'}" small>{{ ucfirst($b->tipo) }}</x-ui.badge></td>
                    <td style="font-size:.8rem;max-width:200px" class="text-truncate">{{ $b->motivo }}</td>
                    <td style="font-size:.8rem">{{ $b->fecha_solicitud?->format('d/m/Y') }}</td>
                    <td><x-ui.badge :type="match($b->estatus){'aprobada'=>'success','activa'=>'primary','cancelada'=>'secondary',default=>'warning'}" small>{{ ucfirst($b->estatus) }}</x-ui.badge></td>
                    <td class="text-end">
                        <a href="{{ route('bajas.show',$b) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin bajas registradas." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @isset($items)
    <div class="px-3 py-2 border-top">{{ $items->links() }}</div>
    @endisset
</x-ui.card>
</x-layouts.app>
