<x-layouts.app page-title="Inventario">
<x-ui.page-header title="Inventario" subtitle="Control de artículos, stock y movimientos por sede.">
    <x-slot name="actions">
        @can('inventario.gestionar')
        <a href="{{ route('inventario.store') }}" class="btn btn-primary btn-sm"
           data-bs-toggle="modal" data-bs-target="#modal-nuevo-item">+ Nuevo artículo</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="route('inventario.index')">
    <x-slot name="fields">
        <div class="col-md-4"><input type="text" name="q" class="form-control form-control-sm" placeholder="Nombre o código..." value="{{ request('q') }}"></div>
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
            <thead><tr><th>Artículo</th><th>Sede</th><th>Stock</th><th>Mín</th><th>Precio</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                @forelse($items as $item)
                @php $enRiesgo = $item->necesitaReposicion(); @endphp
                <tr class="{{ $enRiesgo ? 'table-warning bg-opacity-25' : '' }}">
                    <td>
                        <div class="fw-medium" style="font-size:.875rem">{{ $item->nombre }}</div>
                        <small class="text-muted">{{ $item->codigo ?? '—' }} · {{ $item->unidad_medida }}</small>
                    </td>
                    <td style="font-size:.8rem">{{ $item->sede?->nombre }}</td>
                    <td>
                        <span class="fw-semibold {{ $enRiesgo ? 'text-warning' : 'text-success' }}">{{ $item->stock_actual }}</span>
                    </td>
                    <td style="font-size:.8rem">{{ $item->stock_minimo }}</td>
                    <td style="font-size:.8rem">${{ $item->precio_unitario ? number_format($item->precio_unitario,2) : '—' }}</td>
                    <td>
                        @if($enRiesgo)
                            <x-ui.badge type="warning" small>⚠ Reposición</x-ui.badge>
                        @else
                            <x-ui.badge type="success" small>OK</x-ui.badge>
                        @endif
                    </td>
                    <td class="text-end">
                        @can('inventario.gestionar')
                        <button type="button" class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal" data-bs-target="#modal-mov-{{ $item->id }}">
                            Movimiento
                        </button>
                        {{-- Modal movimiento --}}
                        <x-ui.modal :id="'modal-mov-'.$item->id" :title="'Movimiento: '.$item->nombre" size="sm">
                            <form method="POST" action="{{ route('inventario.movimiento',$item->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Tipo</label>
                                    <select name="tipo" class="form-select form-select-sm" required>
                                        <option value="entrada">Entrada</option>
                                        <option value="salida">Salida</option>
                                        <option value="ajuste">Ajuste</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Cantidad</label>
                                    <input type="number" name="cantidad" class="form-control form-control-sm" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Motivo</label>
                                    <input type="text" name="motivo" class="form-control form-control-sm" placeholder="Opcional">
                                </div>
                                <x-slot name="footer">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                                </x-slot>
                            </form>
                        </x-ui.modal>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin artículos en inventario." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $items->links() }}</div>
</x-ui.card>
</x-layouts.app>
