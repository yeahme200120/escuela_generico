<x-layouts.app page-title="Nuevo rol">
<x-ui.page-header title="Nuevo rol"
    :items="[['label'=>'Roles','url'=>route('roles.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('roles.store') }}">
@csrf
<div class="row g-3">
    <div class="col-md-5">
        <x-ui.card title="Datos del rol">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre') }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug') }}" placeholder="ej: control_escolar" required>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nivel (1=alto, 99=bajo)</label>
                    <input type="number" name="nivel" class="form-control" value="{{ old('nivel',50) }}" min="1" max="99">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion') }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo" {{ old('activo',1)?'checked':'' }}>
                        <label class="form-check-label" for="activo">Activo</label>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
    <div class="col-md-7">
        <x-ui.card title="Permisos a asignar">
            @foreach($permisos as $modulo => $permsGrupo)
            <div class="mb-3">
                <h6 class="fw-semibold text-muted mb-2" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">{{ $modulo }}</h6>
                <div class="row g-1">
                    @foreach($permsGrupo as $p)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" name="permisos[]" value="{{ $p->id }}"
                                   class="form-check-input" id="perm_{{ $p->id }}"
                                   {{ in_array($p->id, old('permisos',[])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm_{{ $p->id }}" style="font-size:.8rem">
                                {{ $p->accion }}
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <hr>
            @endforeach
        </x-ui.card>
    </div>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Crear rol</button>
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>
