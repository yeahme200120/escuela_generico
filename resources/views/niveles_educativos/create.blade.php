<x-layouts.app page-title="Nuevo Nivel">
<x-ui.page-header title="Nuevo Nivel Educativo" :items="[['label'=>'Niveles','url'=>route('niveles.index')],['label'=>'Nuevo']]"/>
<x-ui.card title="Datos del nivel">
<form method="POST" action="{{ route('niveles.store') }}">
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
                value="{{ old('nombre') }}" placeholder="Secundaria" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Clave</label>
            <input type="text" name="clave" class="form-control" value="{{ old('clave') }}" placeholder="SEC">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Duración (años)</label>
            <input type="number" name="duracion_anos" class="form-control" value="{{ old('duracion_anos',3) }}" min="1" max="10">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Orden</label>
            <input type="number" name="orden" class="form-control" value="{{ old('orden',1) }}" min="0">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check mb-2">
                <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo" checked>
                <label class="form-check-label" for="activo">Activo</label>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="{{ route('niveles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
</x-ui.card>
</x-layouts.app>
