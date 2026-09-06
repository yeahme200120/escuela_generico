<x-layouts.app page-title="Caja">
<x-ui.page-header title="Control de caja" subtitle="Apertura, movimientos y cierre de turnos." />

<div class="row g-3">
    @forelse($cajas as $caja)
    @php $turnoActivo = $caja->turnos->first(); @endphp
    <div class="col-md-6 col-xl-4">
        <x-ui.card :title="$caja->nombre">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <x-ui.badge :type="$turnoActivo ? 'success' : 'secondary'" pill>
                    {{ $turnoActivo ? 'Abierta' : 'Cerrada' }}
                </x-ui.badge>
                <small class="text-muted">{{ $caja->sede?->nombre }}</small>
            </div>

            @if($turnoActivo)
            <dl class="row mb-3" style="font-size:.875rem">
                <dt class="col-6 text-muted">Cajero</dt>
                <dd class="col-6">{{ $turnoActivo->usuario?->nombres }}</dd>
                <dt class="col-6 text-muted">Apertura</dt>
                <dd class="col-6">{{ $turnoActivo->fecha_apertura?->format('H:i') }}</dd>
                <dt class="col-6 text-muted">Monto apertura</dt>
                <dd class="col-6 fw-semibold">${{ number_format($turnoActivo->monto_apertura,2) }}</dd>
            </dl>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('finanzas.caja.turno',$turnoActivo) }}" class="btn btn-outline-primary btn-sm">Ver movimientos</a>
                @can('caja.cerrar')
                <button type="button" class="btn btn-outline-danger btn-sm"
                        data-bs-toggle="modal" data-bs-target="#modal-cerrar-{{ $caja->id }}">
                    Cerrar caja
                </button>
                @endcan
            </div>

            {{-- Modal cierre --}}
            @can('caja.cerrar')
            <x-ui.modal :id="'modal-cerrar-'.$caja->id" title="Cerrar turno de caja">
                <form method="POST" action="{{ route('finanzas.caja.cerrar',$turnoActivo) }}">
                    @csrf @method('POST')
                    <div class="mb-3">
                        <label class="form-label fw-medium">Monto en caja al cierre <span class="text-danger">*</span></label>
                        <input type="number" name="monto_cierre" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"></textarea>
                    </div>
                    <x-slot name="footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger btn-sm">Confirmar cierre</button>
                    </x-slot>
                </form>
            </x-ui.modal>
            @endcan

            @else
            {{-- Caja cerrada --}}
            @can('caja.abrir')
            <form method="POST" action="{{ route('finanzas.caja.abrir',$caja) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium" style="font-size:.875rem">Monto de apertura</label>
                    <input type="number" name="monto_apertura" class="form-control form-control-sm" step="0.01" min="0" value="0" required>
                </div>
                <button type="submit" class="btn btn-success btn-sm w-100">Abrir caja</button>
            </form>
            @endcan
            @endif
        </x-ui.card>
    </div>
    @empty
    <div class="col-12">
        <x-ui.empty-state message="No hay cajas configuradas para esta organización." />
    </div>
    @endforelse
</div>
</x-layouts.app>
