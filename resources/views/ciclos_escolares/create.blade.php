<x-layouts.app page-title="Nuevo Ciclo Escolar">
<x-ui.page-header title="Nuevo Ciclo Escolar" :items="[['label'=>'Ciclos','url'=>route('ciclos.index')],['label'=>'Nuevo']]" />
<x-ui.card title="Datos del ciclo">
<form method="POST" action="{{ route('ciclos.store') }}">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-medium">Escuela <span class="text-danger">*</span></label>
            <select name="escuela_id" class="form-select @error('escuela_id') is-invalid @enderror" required>
                <option value="">Seleccionar...</option>
                @foreach($escuelas as $e)
                <option value="{{ $e->id }}" {{ old('escuela_id')==$e->id?'selected':'' }}>{{ $e->nombre }}</option>
                @endforeach
            </select>
            @error('escuela_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                value="{{ old('nombre') }}" placeholder="2026-2027" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Fecha inicio <span class="text-danger">*</span></label>
            <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror"
                value="{{ old('fecha_inicio') }}" required>
            @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Fecha fin <span class="text-danger">*</span></label>
            <input type="date" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror"
                value="{{ old('fecha_fin') }}" required>
            @error('fecha_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Estado</label>
            <select name="estatus" class="form-select">
                @foreach(['configuracion'=>'Configuración','activo'=>'Activo','cerrado'=>'Cerrado'] as $v=>$l)
                <option value="{{ $v }}" {{ old('estatus','configuracion')==$v?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check mb-2">
                <input type="checkbox" name="es_actual" value="1" class="form-check-input"
                    id="es_actual" {{ old('es_actual')?'checked':'' }}>
                <label class="form-check-label" for="es_actual">Ciclo actual</label>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary">Guardar ciclo</button>
        <a href="{{ route('ciclos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
</x-ui.card>
</x-layouts.app>
