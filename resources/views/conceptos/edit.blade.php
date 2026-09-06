<x-layouts.app page-title="Editar Conceptos de Pago">
<x-ui.page-header title="Editar Conceptos de Pago"
    :items="[['label'=>'Conceptos de Pago','url'=>route('finanzas.conceptos.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('finanzas.conceptos.update',$item) }}">
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
                    <label class="form-label fw-medium">Tipo</label>
                    <input type="text" name="tipo" class="form-control @error('tipo') is-invalid @enderror"
                           value="{{ old('tipo',$item->tipo) }}">
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Importe Default</label>
                    <input type="text" name="importe_default" class="form-control @error('importe_default') is-invalid @enderror"
                           value="{{ old('importe_default',$item->importe_default) }}">
                    @error('importe_default')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('finanzas.conceptos.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>