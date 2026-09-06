<x-layouts.app page-title="Nuevo Materias">
<x-ui.page-header title="Nuevo Materias"
    :items="[['label'=>'Materias','url'=>route('materias.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('materias.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nombre</label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}">
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Clave</label>
                    <input type="text" name="clave" class="form-control @error('clave') is-invalid @enderror"
                           value="{{ old('clave') }}">
                    @error('clave')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Horas Semana</label>
                    <input type="text" name="horas_semana" class="form-control @error('horas_semana') is-invalid @enderror"
                           value="{{ old('horas_semana') }}">
                    @error('horas_semana')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Creditos</label>
                    <input type="text" name="creditos" class="form-control @error('creditos') is-invalid @enderror"
                           value="{{ old('creditos') }}">
                    @error('creditos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>