<x-layouts.app page-title="Editar Trayectorias">
<x-ui.page-header title="Editar Trayectorias"
    :items="[['label'=>'Trayectorias','url'=>route('trayectorias.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('trayectorias.update',$item) }}">
@csrf @method('PUT')
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Estatus</label>
                    <input type="text" name="estatus" class="form-control @error('estatus') is-invalid @enderror"
                           value="{{ old('estatus',$item->estatus) }}">
                    @error('estatus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Situacion Academica</label>
                    <input type="text" name="situacion_academica" class="form-control @error('situacion_academica') is-invalid @enderror"
                           value="{{ old('situacion_academica',$item->situacion_academica) }}">
                    @error('situacion_academica')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Fecha Inicio</label>
                    <input type="text" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror"
                           value="{{ old('fecha_inicio',$item->fecha_inicio) }}">
                    @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('trayectorias.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>