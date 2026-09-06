<x-layouts.app page-title="Editar Mantenimiento">
<x-ui.page-header title="Editar Mantenimiento"
    :items="[['label'=>'Mantenimiento','url'=>route('mantenimientos.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('mantenimientos.update',$item) }}">
@csrf @method('PUT')
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Titulo</label>
                    <input type="text" name="titulo" class="form-control @error('titulo') is-invalid @enderror"
                           value="{{ old('titulo',$item->titulo) }}">
                    @error('titulo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Prioridad</label>
                    <input type="text" name="prioridad" class="form-control @error('prioridad') is-invalid @enderror"
                           value="{{ old('prioridad',$item->prioridad) }}">
                    @error('prioridad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Estado</label>
                    <input type="text" name="estado" class="form-control @error('estado') is-invalid @enderror"
                           value="{{ old('estado',$item->estado) }}">
                    @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Descripcion</label>
                    <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                           value="{{ old('descripcion',$item->descripcion) }}">
                    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('mantenimientos.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>