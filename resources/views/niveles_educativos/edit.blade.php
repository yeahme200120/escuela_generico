<x-layouts.app page-title="Editar Nivel">
<x-ui.page-header title="Editar: {{ $nivel->nombre }}" :items="[['label'=>'Niveles','url'=>route('niveles.index')],['label'=>'Editar']]"/>
<x-ui.card title="Datos del nivel">
<form method="POST" action="{{ route('niveles.update', $nivel) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-medium">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $nivel->nombre) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Duración (años)</label>
            <input type="number" name="duracion_anos" class="form-control" value="{{ old('duracion_anos', $nivel->duracion_anos) }}" min="1">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Orden</label>
            <input type="number" name="orden" class="form-control" value="{{ old('orden', $nivel->orden) }}">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check mb-2">
                <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo"
                    {{ $nivel->activo ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">Activo</label>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('niveles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
</x-ui.card>
</x-layouts.app>
