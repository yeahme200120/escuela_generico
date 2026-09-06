<x-layouts.app page-title="Nuevo Trayectorias">
<x-ui.page-header title="Nuevo Trayectorias"
    :items="[['label'=>'Trayectorias','url'=>route('trayectorias.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('trayectorias.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Estatus</label>
                    <input type="text" name="estatus" class="form-control @error('estatus') is-invalid @enderror"
                           value="{{ old('estatus') }}">
                    @error('estatus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Situacion Academica</label>
                    <input type="text" name="situacion_academica" class="form-control @error('situacion_academica') is-invalid @enderror"
                           value="{{ old('situacion_academica') }}">
                    @error('situacion_academica')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Fecha Inicio</label>
                    <input type="text" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror"
                           value="{{ old('fecha_inicio') }}">
                    @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('trayectorias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>