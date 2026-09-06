<x-layouts.app page-title="Cargos">
<x-ui.page-header title="Cargos de alumnos" subtitle="Gestión de cobros, conceptos y vencimientos.">
    <x-slot name="actions">
        @can('pagos.registrar')
        <a href="{{ route('finanzas.cargos.create') }}" class="btn btn-primary btn-sm">+ Nuevo cargo</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="route('finanzas.cargos.index')">
    <x-slot name="fields">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Nombre o matrícula del alumno..." value="{{ request('q') }}">
        </div>
        <div class="col-md-2">
            <select name="estado" class="form-select form-select-sm">
                <option value="">Estado</option>
                @foreach(['pendiente','parcial','pagado','cancelado','vencido'] as $e)
                <option value="{{ $e }}" {{ request('estado')===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="sede_id" class="form-select form-select-sm">
                <option value="">Sede</option>
                @foreach(\App\Models\Sede::whereHas('organizacion',fn($q)=>$q->where('id',auth()->user()->organizacion_id))->activas()->get() as $s)
                <option value="{{ $s->id }}" {{ request('sede_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr><th>Alumno</th><th>Concepto</th><th>Importe</th><th>Total</th><th>Vencimiento</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($cargos as $c)
                <tr class="{{ $c->estado==='vencido'?'table-danger bg-opacity-10':'' }}">
                    <td style="font-size:.875rem">
                        <div class="fw-medium">{{ $c->alumno?->nombre_completo }}</div>
                        <small class="text-muted">{{ $c->alumno?->matricula }}</small>
                    </td>
                    <td style="font-size:.875rem">
                        {{ $c->concepto?->nombre }}
                        @if($c->referencia)<small class="d-block text-muted">{{ $c->referencia }}</small>@endif
                    </td>
                    <td style="font-size:.875rem">${{ number_format($c->importe,2) }}</td>
                    <td class="fw-semibold">${{ number_format($c->total,2) }}</td>
                    <td style="font-size:.8rem">{{ $c->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                    <td>
                        <x-ui.badge :type="match($c->estado){
                            'pendiente'=>'warning','pagado'=>'success',
                            'parcial'=>'info','cancelado'=>'secondary','vencido'=>'danger',default=>'secondary'
                        }">{{ ucfirst($c->estado) }}</x-ui.badge>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('alumnos.show',$c->alumno_id) }}" class="btn btn-sm btn-outline-secondary">Alumno</a>
                        @if(in_array($c->estado,['pendiente','parcial','vencido']))
                        <a href="{{ route('finanzas.pagos.create',['alumno_id'=>$c->alumno_id]) }}"
                           class="btn btn-sm btn-outline-success ms-1">Cobrar</a>
                        @endif
                        @if(in_array($c->estado,['pendiente','vencido']))
                        @can('pagos.cancelar')
                        <x-ui.confirm id="cancel-cargo-{{ $c->id }}"
                            title="¿Cancelar cargo?"
                            message="El cargo quedará cancelado y no se podrá cobrar."
                            :action="route('finanzas.cargos.destroy',$c)"
                            method="DELETE" label="Cancelar cargo" type="danger">
                            <x-slot name="trigger">
                                <button type="button" class="btn btn-sm btn-outline-danger ms-1"
                                    data-bs-toggle="modal" data-bs-target="#cancel-cargo-{{ $c->id }}">✕</button>
                            </x-slot>
                        </x-ui.confirm>
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin cargos registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top d-flex justify-content-between">
        <small class="text-muted">{{ $cargos->total() }} registros</small>
        {{ $cargos->links() }}
    </div>
</x-ui.card>
</x-layouts.app>
