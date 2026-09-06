<x-layouts.app page-title="Editar Planes de Estudio">
<x-ui.page-header title="Editar Planes de Estudio"
    :items="[['label'=>'Planes de Estudio','url'=>route('planes.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('planes.update',$item) }}">
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
                    <label class="form-label fw-medium">Descripcion</label>
                    <input type="text" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror"
                           value="{{ old('descripcion',$item->descripcion) }}">
                    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('planes.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>