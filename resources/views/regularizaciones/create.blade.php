<x-layouts.app page-title="Nuevo Regularizaciones">
<x-ui.page-header title="Nuevo Regularizaciones"
    :items="[['label'=>'Regularizaciones','url'=>route('regularizaciones.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('regularizaciones.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Resultado</label>
                    <input type="text" name="resultado" class="form-control @error('resultado') is-invalid @enderror"
                           value="{{ old('resultado') }}">
                    @error('resultado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Fecha</label>
                    <input type="text" name="fecha" class="form-control @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha') }}">
                    @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('regularizaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>