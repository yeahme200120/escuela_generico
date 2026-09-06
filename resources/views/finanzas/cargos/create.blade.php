<x-layouts.app page-title="Nuevo Cargo">
<x-ui.page-header title="Registrar cargo"
    :items="[['label'=>'Cargos','url'=>route('finanzas.cargos.index')],['label'=>'Nuevo']]" />

<form method="POST" action="{{ route('finanzas.cargos.store') }}">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Datos del cargo">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Alumno <span class="text-danger">*</span></label>
                    <select name="alumno_id" class="form-select @error('alumno_id') is-invalid @enderror" required>
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
                    <label class="form-label fw-medium">Concepto <span class="text-danger">*</span></label>
                    <select name="concepto_id" class="form-select @error('concepto_id') is-invalid @enderror" required
                            onchange="setImporte(this)">
                        <option value="">Seleccionar concepto...</option>
                        @foreach($conceptos as $c)
                        <option value="{{ $c->id }}" data-importe="{{ $c->importe_default }}"
                                {{ old('concepto_id')==$c->id?'selected':'' }}>
                            {{ $c->nombre }} — ${{ number_format($c->importe_default,2) }}
                        </option>
                        @endforeach
                    </select>
                    @error('concepto_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Ciclo escolar <span class="text-danger">*</span></label>
                    <select name="ciclo_escolar_id" class="form-select @error('ciclo_escolar_id') is-invalid @enderror" required>
                        @foreach($ciclos as $c)
                        <option value="{{ $c->id }}" {{ ($c->es_actual || old('ciclo_escolar_id')==$c->id)?'selected':'' }}>
                            {{ $c->nombre }}
                        </option>
                        @endforeach
                    </select>
                    @error('ciclo_escolar_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                <div class="col-md-3">
                    <label class="form-label fw-medium">Importe <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="importe" id="inp-importe" class="form-control @error('importe') is-invalid @enderror"
                               step="0.01" min="0" value="{{ old('importe',0) }}" required onchange="calcTotal()">
                    </div>
                    @error('importe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Descuento</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="descuento" id="inp-descuento" class="form-control"
                               step="0.01" min="0" value="{{ old('descuento',0) }}" onchange="calcTotal()">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Recargo</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="recargo" id="inp-recargo" class="form-control"
                               step="0.01" min="0" value="{{ old('recargo',0) }}" onchange="calcTotal()">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-medium">Total calculado</label>
                    <div class="form-control fw-bold bg-light" id="display-total">$0.00</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Fecha de vencimiento</label>
                    <input type="date" name="fecha_vencimiento" class="form-control"
                           value="{{ old('fecha_vencimiento') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Referencia</label>
                    <input type="text" name="referencia" class="form-control" value="{{ old('referencia') }}"
                           placeholder="Folio, número, descripción corta">
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Registrar cargo</button>
    <a href="{{ route('finanzas.cargos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>

@push('scripts')
<script>
function setImporte(sel) {
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('inp-importe').value = opt.dataset.importe || 0;
    calcTotal();
}
function calcTotal() {
    const imp  = parseFloat(document.getElementById('inp-importe').value || 0);
    const desc = parseFloat(document.getElementById('inp-descuento').value || 0);
    const rec  = parseFloat(document.getElementById('inp-recargo').value || 0);
    const total = Math.max(0, imp - desc + rec);
    document.getElementById('display-total').textContent = '$' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,',');
}
calcTotal();
</script>
@endpush
</x-layouts.app>
