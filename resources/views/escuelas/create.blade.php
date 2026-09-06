<x-layouts.app page-title="Nuevo Escuelas">
<x-ui.page-header title="Nuevo Escuelas"
    :items="[['label'=>'Escuelas','url'=>route('escuelas.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('escuelas.store') }}">
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
                    <label class="form-label fw-medium">Email</label>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Telefono</label>
                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono') }}">
                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control @error('ciudad') is-invalid @enderror"
                           value="{{ old('ciudad') }}">
                    @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('escuelas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>