<x-layouts.app page-title="Editar Grupos">
<x-ui.page-header title="Editar Grupos"
    :items="[['label'=>'Grupos','url'=>route('grupos.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('grupos.update',$item) }}">
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
                    <label class="form-label fw-medium">Turno</label>
                    <input type="text" name="turno" class="form-control @error('turno') is-invalid @enderror"
                           value="{{ old('turno',$item->turno) }}">
                    @error('turno')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
    <a href="{{ route('grupos.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>