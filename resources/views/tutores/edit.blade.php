<x-layouts.app page-title="Editar Tutores">
<x-ui.page-header title="Editar Tutores"
    :items="[['label'=>'Tutores','url'=>route('tutores.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ route('tutores.update',$item) }}">
@csrf @method('PUT')
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nombres</label>
                    <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror"
                           value="{{ old('nombres',$item->nombres) }}">
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Apellido Paterno</label>
                    <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror"
                           value="{{ old('apellido_paterno',$item->apellido_paterno) }}">
                    @error('apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Email</label>
                    <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email',$item->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Telefono</label>
                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                           value="{{ old('telefono',$item->telefono) }}">
                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Parentesco</label>
                    <input type="text" name="parentesco" class="form-control @error('parentesco') is-invalid @enderror"
                           value="{{ old('parentesco',$item->parentesco) }}">
                    @error('parentesco')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="{{ route('tutores.show',$item) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>