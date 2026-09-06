<x-layouts.app page-title="Editar Parcialidades">
<x-ui.page-header title="Editar Parcialidades"
    :items="[['label'=>'Parcialidades','url'=>route('parcialidades.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('parcialidades.update',$item) }}">
@csrf @method('PUT')
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Numero</label>
                    <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror"
                           value="{{ old('numero',$item->numero) }}">
                    @error('numero')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Fecha Vencimiento</label>
                    <input type="text" name="fecha_vencimiento" class="form-control @error('fecha_vencimiento') is-invalid @enderror"
                           value="{{ old('fecha_vencimiento',$item->fecha_vencimiento) }}">
                    @error('fecha_vencimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Importe</label>
                    <input type="text" name="importe" class="form-control @error('importe') is-invalid @enderror"
                           value="{{ old('importe',$item->importe) }}">
                    @error('importe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('parcialidades.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>