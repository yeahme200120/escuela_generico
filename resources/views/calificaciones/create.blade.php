<x-layouts.app page-title="Nuevo Calificaciones">
<x-ui.page-header title="Nuevo Calificaciones"
    :items="[['label'=>'Calificaciones','url'=>route('calificaciones.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('calificaciones.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Calificacion</label>
                    <input type="text" name="calificacion" class="form-control @error('calificacion') is-invalid @enderror"
                           value="{{ old('calificacion') }}">
                    @error('calificacion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Resultado</label>
                    <input type="text" name="resultado" class="form-control @error('resultado') is-invalid @enderror"
                           value="{{ old('resultado') }}">
                    @error('resultado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Observaciones</label>
                    <input type="text" name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                           value="{{ old('observaciones') }}">
                    @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('calificaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>