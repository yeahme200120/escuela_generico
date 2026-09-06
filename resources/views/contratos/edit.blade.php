<x-layouts.app page-title="Editar Contratos">
<x-ui.page-header title="Editar Contratos"
    :items="[['label'=>'Contratos','url'=>route('contratos.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('contratos.update',$item) }}">
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
                <div class="col-md-6">
                    <label class="form-label fw-medium">Salario</label>
                    <input type="text" name="salario" class="form-control @error('salario') is-invalid @enderror"
                           value="{{ old('salario',$item->salario) }}">
                    @error('salario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('contratos.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>