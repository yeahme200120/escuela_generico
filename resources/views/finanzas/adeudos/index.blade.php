<x-layouts.app page-title="Adeudos">
<x-ui.page-header title="Cartera de adeudos" subtitle="Alumnos con cargos pendientes, parciales o vencidos." />

<x-ui.filter-bar :action="request()->url()">
    <x-slot name="fields">
        <div class="col-md-3"><input type="text" name="q" class="form-control form-control-sm" placeholder="Alumno o matrícula..." value="{{ request('q') }}"></div>
        <div class="col-md-2">
            <select name="sede_id" class="form-select form-select-sm">
                <option value="">Sede</option>
                @foreach($sedes as $s)<option value="{{ $s->id }}" {{ request('sede_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="estado" class="form-select form-select-sm">
                <option value="">Estado cargo</option>
                <option value="pendiente" {{ request('estado')==='pendiente'?'selected':'' }}>Pendiente</option>
                <option value="parcial"   {{ request('estado')==='parcial'  ?'selected':'' }}>Parcial</option>
                <option value="vencido"   {{ request('estado')==='vencido'  ?'selected':'' }}>Vencido</option>
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

{{-- Resumen aging --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3"><x-ui.stat-card label="Total adeudos" :value="'$'.number_format($totalAdeudos,2)" color="danger" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="Alumnos con adeudo" :value="$totalAlumnos" color="warning" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="Vencidos" :value="'$'.number_format($totalVencidos,2)" color="danger" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="Pendientes" :value="'$'.number_format($totalPendientes,2)" color="warning" /></div>
</div>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Alumno</th><th>Concepto</th><th>Total</th><th>Pagado</th><th>Saldo</th><th>Vence</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                @forelse($cargos as $c)
                <tr class="{{ $c->estado==='vencido'?'table-danger bg-opacity-10':'' }}">
                    <td>
                        <div class="fw-medium" style="font-size:.875rem">{{ $c->alumno?->nombre_completo }}</div>
                        <small class="text-muted">{{ $c->alumno?->matricula }}</small>
                    </td>
                    <td style="font-size:.875rem">{{ $c->concepto?->nombre }}</td>
                    <td class="fw-semibold">${{ number_format($c->total,2) }}</td>
                    <td class="text-success">${{ number_format($c->pagoDetalles->sum('importe_aplicado'),2) }}</td>
                    <td class="fw-bold text-danger">${{ number_format($c->total - $c->pagoDetalles->sum('importe_aplicado'),2) }}</td>
                    <td style="font-size:.8rem" class="{{ $c->fecha_vencimiento?->isPast()?'text-danger fw-bold':'' }}">
                        {{ $c->fecha_vencimiento?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td>
                        <x-ui.badge :type="match($c->estado){'pendiente'=>'warning','parcial'=>'info','vencido'=>'danger',default=>'secondary'}" small>
                            {{ ucfirst($c->estado) }}
                        </x-ui.badge>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('finanzas.pagos.create',['alumno_id'=>$c->alumno_id]) }}" class="btn btn-sm btn-outline-success">Cobrar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8"><x-ui.empty-state message="Sin adeudos con los filtros aplicados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top d-flex justify-content-between">
        <small class="text-muted">{{ $cargos->total() }} cargos</small>
        {{ $cargos->links() }}
    </div>
</x-ui.card>
</x-layouts.app>
