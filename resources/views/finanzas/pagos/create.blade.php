<x-layouts.app page-title="Registrar Pago">
<x-ui.page-header title="Registrar Pago"
    :items="[['label'=>'Pagos','url'=>route('finanzas.pagos.index')],['label'=>'Nuevo']]" />

<form method="POST" action="{{ route('finanzas.pagos.store') }}" id="form-pago">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Datos del pago">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Alumno <span class="text-danger">*</span></label>
                    <select name="alumno_id" class="form-select @error('alumno_id') is-invalid @enderror" required id="sel-alumno"
                            onchange="window.location.href='{{ route('finanzas.pagos.create') }}?alumno_id='+this.value">
                        <option value="">Seleccionar alumno...</option>
                        @foreach($alumnos as $a)
                        <option value="{{ $a->id }}" {{ (old('alumno_id', request('alumno_id'))==$a->id)?'selected':'' }}>
                            {{ $a->nombre_completo }} — {{ $a->matricula }}
                        </option>
                        @endforeach
                    </select>
                    @error('alumno_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Sede <span class="text-danger">*</span></label>
                    <select name="sede_id" class="form-select @error('sede_id') is-invalid @enderror" required>
                        @foreach($sedes as $s)
                        <option value="{{ $s->id }}" {{ old('sede_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                    @error('sede_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Fecha de pago <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_pago" class="form-control @error('fecha_pago') is-invalid @enderror"
                           value="{{ old('fecha_pago', now()->format('Y-m-d')) }}" required>
                    @error('fecha_pago')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Método de pago <span class="text-danger">*</span></label>
                    <select name="metodo_pago_id" class="form-select @error('metodo_pago_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($metodos as $m)
                        <option value="{{ $m->id }}" {{ old('metodo_pago_id')==$m->id?'selected':'' }}>{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                    @error('metodo_pago_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Referencia</label>
                    <input type="text" name="referencia" class="form-control" value="{{ old('referencia') }}" placeholder="Folio / número de recibo">
                </div>
            </div>
        </x-ui.card>

        @if($alumnoSeleccionado && $cargos->count())
        <x-ui.card title="Cargos pendientes del alumno" class="mt-3" :flush="true">
            @php $totalCargos = 0; @endphp
            <div class="table-responsive">
                <table class="table table-se mb-0">
                    <thead><tr><th style="width:30px"></th><th>Concepto</th><th>Total</th><th>Vence</th><th>Estado</th></tr></thead>
                    <tbody>
                        @foreach($cargos as $i => $c)
                        @php $totalCargos += $c->total; @endphp
                        <tr>
                            <td>
                                <input type="checkbox" name="cargos[{{ $i }}][cargo_id]" value="{{ $c->id }}"
                                       class="form-check-input chk-cargo" data-importe="{{ $c->total }}"
                                       id="cargo_{{ $c->id }}" checked onchange="calcTotal()">
                                <input type="hidden" name="cargos[{{ $i }}][importe_aplicado]" id="imp_{{ $c->id }}" value="{{ $c->total }}">
                            </td>
                            <td style="font-size:.875rem">
                                <label for="cargo_{{ $c->id }}" class="fw-medium mb-0">{{ $c->concepto?->nombre }}</label>
                                @if($c->referencia)<small class="d-block text-muted">{{ $c->referencia }}</small>@endif
                            </td>
                            <td class="fw-semibold">${{ number_format($c->total,2) }}</td>
                            <td style="font-size:.8rem">{{ $c->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                            <td><x-ui.badge :type="$c->estado==='vencido'?'danger':'warning'" small>{{ ucfirst($c->estado) }}</x-ui.badge></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
        @elseif($alumnoSeleccionado)
        <x-ui.card class="mt-3">
            <x-ui.empty-state message="Este alumno no tiene cargos pendientes." />
        </x-ui.card>
        @endif
    </div>

    <div class="col-md-4">
        <x-ui.card title="Resumen del pago">
            <div class="mb-3">
                <label class="form-label fw-medium">Importe total <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="importe" id="inp-importe" class="form-control fw-bold"
                           step="0.01" min="0.01" value="{{ old('importe', $alumnoSeleccionado ? $cargos->sum('total') : '') }}"
                           required placeholder="0.00">
                </div>
                @error('importe')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            @if($alumnoSeleccionado)
            <div class="alert alert-info py-2 mb-3" style="font-size:.8rem">
                <strong>Total cargos seleccionados:</strong>
                <span id="total-display">${{ number_format($cargos->sum('total'),2) }}</span>
            </div>
            @endif
            <button type="submit" class="btn btn-success w-100 fw-bold">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                    <path d="M8 1a7 7 0 1 1 0 14A7 7 0 0 1 8 1zm0 1a6 6 0 1 0 0 12A6 6 0 0 0 8 2zM6.5 5.5a1 1 0 0 1 2 0V8h2a1 1 0 1 1 0 2H8.5V8h-2V5.5z"/>
                </svg>
                Registrar pago
            </button>
            <a href="{{ route('finanzas.pagos.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
        </x-ui.card>
    </div>
</div>
</form>

@push('scripts')
<script>
function calcTotal() {
    let total = 0;
    document.querySelectorAll('.chk-cargo:checked').forEach(chk => {
        total += parseFloat(chk.dataset.importe || 0);
    });
    const disp = document.getElementById('total-display');
    const inp  = document.getElementById('inp-importe');
    if (disp) disp.textContent = '$' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    if (inp)  inp.value = total.toFixed(2);
}
</script>
@endpush
</x-layouts.app>
