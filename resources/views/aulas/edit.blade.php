<x-layouts.app page-title="Editar Aulas">
<x-ui.page-header title="Editar Aulas"
    :items="[['label'=>'Aulas','url'=>route('aulas.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('aulas.update',$item) }}">
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
                    <label class="form-label fw-medium">Tipo</label>
                    <input type="text" name="tipo" class="form-control @error('tipo') is-invalid @enderror"
                           value="{{ old('tipo',$item->tipo) }}">
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Capacidad</label>
                    <input type="text" name="capacidad" class="form-control @error('capacidad') is-invalid @enderror"
                           value="{{ old('capacidad',$item->capacidad) }}">
                    @error('capacidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('aulas.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>