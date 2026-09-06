<x-layouts.app page-title="Prospectos">
<x-ui.page-header title="Prospectos — CRM de admisiones">
    <x-slot name="actions">
        <a href="{{ route('admisiones.prospectos.store') }}"
           class="btn btn-primary btn-sm"
           data-bs-toggle="modal" data-bs-target="#modal-nuevo-prospecto">
            + Nuevo prospecto
        </a>
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="route('admisiones.prospectos.index')">
    <x-slot name="fields">
        <div class="col-md-4"><input type="text" name="q" class="form-control form-control-sm" placeholder="Nombre o email..." value="{{ request('q') }}"></div>
        <div class="col-md-2">
            <select name="estatus" class="form-select form-select-sm">
                <option value="">Estatus</option>
                @foreach(['nuevo','contactado','citado','evaluado','admitido','rechazado','cancelado'] as $e)
                <option value="{{ $e }}" {{ request('estatus')===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

{{-- Pipeline kanban simple --}}
@php
$totalPorEstatus = $prospectos->groupBy('estatus');
$estatusCols = ['nuevo'=>'secondary','contactado'=>'info','citado'=>'primary','evaluado'=>'warning','admitido'=>'success','rechazado'=>'danger'];
@endphp
<div class="d-flex gap-2 mb-3 overflow-auto pb-1">
    @foreach($estatusCols as $est=>$color)
    <div class="badge text-bg-{{ $color }} py-2 px-3" style="font-size:.8rem;white-space:nowrap">
        {{ ucfirst($est) }}: {{ $totalPorEstatus->get($est,collect())->count() }}
    </div>
    @endforeach
</div>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Prospecto</th><th>Contacto</th><th>Nivel interés</th><th>Sede interés</th><th>Estatus</th><th></th></tr></thead>
            <tbody>
                @forelse($prospectos as $p)
                <tr>
                    <td>
                        <div class="fw-medium" style="font-size:.875rem">{{ $p->nombre_completo }}</div>
                        <small class="text-muted">{{ $p->created_at?->diffForHumans() }}</small>
                    </td>
                    <td style="font-size:.8rem">
                        <div>{{ $p->email ?? '—' }}</div>
                        <div>{{ $p->telefono ?? '—' }}</div>
                    </td>
                    <td style="font-size:.8rem">{{ $p->nivel_interes ?? '—' }}</td>
                    <td style="font-size:.8rem">{{ $p->sedeInteres?->nombre ?? '—' }}</td>
                    <td>
                        <x-ui.badge :type="$estatusCols[$p->estatus] ?? 'secondary'" small>{{ ucfirst($p->estatus) }}</x-ui.badge>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admisiones.prospectos.show',$p->id) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin prospectos registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $prospectos->links() }}</div>
</x-ui.card>
</x-layouts.app>
