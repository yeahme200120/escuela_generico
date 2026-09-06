<x-layouts.app page-title="Editar Bajas">
<x-ui.page-header title="Editar Bajas"
    :items="[['label'=>'Bajas','url'=>route('bajas.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('bajas.update',$item) }}">
@csrf @method('PUT')
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Tipo</label>
                    <input type="text" name="tipo" class="form-control @error('tipo') is-invalid @enderror"
                           value="{{ old('tipo',$item->tipo) }}">
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Fecha Solicitud</label>
                    <input type="text" name="fecha_solicitud" class="form-control @error('fecha_solicitud') is-invalid @enderror"
                           value="{{ old('fecha_solicitud',$item->fecha_solicitud) }}">
                    @error('fecha_solicitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Motivo</label>
                    <input type="text" name="motivo" class="form-control @error('motivo') is-invalid @enderror"
                           value="{{ old('motivo',$item->motivo) }}">
                    @error('motivo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('bajas.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>