<x-layouts.app page-title="Editar Calificaciones">
<x-ui.page-header title="Editar Calificaciones"
    :items="[['label'=>'Calificaciones','url'=>route('calificaciones.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('calificaciones.update',$item) }}">
@csrf @method('PUT')
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Calificacion</label>
                    <input type="text" name="calificacion" class="form-control @error('calificacion') is-invalid @enderror"
                           value="{{ old('calificacion',$item->calificacion) }}">
                    @error('calificacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Resultado</label>
                    <input type="text" name="resultado" class="form-control @error('resultado') is-invalid @enderror"
                           value="{{ old('resultado',$item->resultado) }}">
                    @error('resultado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Observaciones</label>
                    <input type="text" name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                           value="{{ old('observaciones',$item->observaciones) }}">
                    @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('calificaciones.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>