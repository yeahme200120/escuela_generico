<x-layouts.app page-title="Nuevo Grados">
<x-ui.page-header title="Nuevo Grados"
    :items="[['label'=>'Grados','url'=>route('grados.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('grados.store') }}">
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
                    <label class="form-label fw-medium">Orden</label>
                    <input type="text" name="orden" class="form-control @error('orden') is-invalid @enderror"
                           value="{{ old('orden') }}">
                    @error('orden')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('grados.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>