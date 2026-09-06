<x-layouts.app page-title="Editar Edificios">
<x-ui.page-header title="Editar Edificios"
    :items="[['label'=>'Edificios','url'=>route('edificios.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('edificios.update',$item) }}">
@csrf @method('PUT')
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nombre</label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre',$item->nombre) }}">
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Clave</label>
                    <input type="text" name="clave" class="form-control @error('clave') is-invalid @enderror"
                           value="{{ old('clave',$item->clave) }}">
                    @error('clave')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Numero Pisos</label>
                    <input type="text" name="numero_pisos" class="form-control @error('numero_pisos') is-invalid @enderror"
                           value="{{ old('numero_pisos',$item->numero_pisos) }}">
                    @error('numero_pisos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('edificios.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>