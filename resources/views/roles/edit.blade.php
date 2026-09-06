<x-layouts.app page-title="Editar rol">
<x-ui.page-header title="Editar: {{ $rol->nombre }}"
    :items="[['label'=>'Roles','url'=>route('roles.index')],['label'=>$rol->nombre,'url'=>route('roles.show',$rol)],['label'=>'Editar']]" />
<form method="POST" action="{{ route('roles.update',$rol) }}">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-5">
        <x-ui.card title="Datos del rol">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-medium">Nombre</label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre',$rol->nombre) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Slug</label>
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                           value="{{ old('slug',$rol->slug) }}" required>
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nivel</label>
                    <input type="number" name="nivel" class="form-control" value="{{ old('nivel',$rol->nivel) }}" min="1" max="99">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion',$rol->descripcion) }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo"
                               {{ old('activo',$rol->activo) ? 'checked':'' }}>
                        <label class="form-check-label" for="activo">Activo</label>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
    <div class="col-md-7">
        <x-ui.card title="Permisos">
            <div class="d-flex gap-2 mb-3">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.querySelectorAll('[name=\'permisos[]\']').forEach(c=>c.checked=true)">Seleccionar todos</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.querySelectorAll('[name=\'permisos[]\']').forEach(c=>c.checked=false)">Limpiar</button>
            </div>
            @foreach($permisos as $modulo => $permsGrupo)
            <div class="mb-3">
                <h6 class="fw-semibold text-muted mb-2" style="font-size:.75rem;text-transform:uppercase">{{ $modulo }}</h6>
                <div class="row g-1">
                    @foreach($permsGrupo as $p)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" name="permisos[]" value="{{ $p->id }}"
                                   class="form-check-input" id="perm_{{ $p->id }}"
                                   {{ in_array($p->id, $asignados) ? 'checked' : '' }}>
                            <label class="form-check-label" for="perm_{{ $p->id }}" style="font-size:.8rem">{{ $p->accion }}</label>
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
    <button type="submit" class="btn btn-primary">Actualizar rol</button>
    <a href="{{ route('roles.show',$rol) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>
