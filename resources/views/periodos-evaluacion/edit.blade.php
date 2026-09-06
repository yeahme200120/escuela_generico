<x-layouts.app page-title="Editar Periodos de Evaluación">
<x-ui.page-header title="Editar Periodos de Evaluación"
    :items="[['label'=>'Periodos de Evaluación','url'=>route('periodos-evaluacion.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('periodos-evaluacion.update',$item) }}">
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
                    <label class="form-label fw-medium">Numero</label>
                    <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror"
                           value="{{ old('numero',$item->numero) }}">
                    @error('numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Fecha Inicio</label>
                    <input type="text" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror"
                           value="{{ old('fecha_inicio',$item->fecha_inicio) }}">
                    @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Fecha Fin</label>
                    <input type="text" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror"
                           value="{{ old('fecha_fin',$item->fecha_fin) }}">
                    @error('fecha_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('periodos-evaluacion.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>