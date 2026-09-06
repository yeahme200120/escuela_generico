<x-layouts.app page-title="Nuevo usuario">
<x-ui.page-header title="Nuevo usuario"
    :items="[['label'=>'Usuarios','url'=>route('users.index')],['label'=>'Nuevo']]" />

<form method="POST" action="{{ route('users.store') }}">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Datos personales">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror"
                           value="{{ old('nombres') }}" required>
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Apellido paterno <span class="text-danger">*</span></label>
                    <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror"
                           value="{{ old('apellido_paterno') }}" required>
                    @error('apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Apellido materno</label>
                    <input type="text" name="apellido_materno" class="form-control"
                           value="{{ old('apellido_materno') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nombre de usuario</label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username') }}" placeholder="Opcional">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Confirmar contraseña <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo" {{ old('activo',1)?'checked':'' }}>
                        <label class="form-check-label" for="activo">Usuario activo</label>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
    <div class="col-md-4">
        <x-ui.card title="Roles">
            @foreach($roles as $rol)
            <div class="form-check py-1">
                <input type="checkbox" name="roles[]" value="{{ $rol->id }}" class="form-check-input"
                       id="rol_{{ $rol->id }}" {{ in_array($rol->id, old('roles',[]))?'checked':'' }}>
                <label class="form-check-label" for="rol_{{ $rol->id }}" style="font-size:.875rem">
                    {{ $rol->nombre }}
                    <span class="badge text-bg-secondary ms-1" style="font-size:.65rem">nv.{{ $rol->nivel }}</span>
                </label>
            </div>
            @endforeach
        </x-ui.card>
        <x-ui.card title="Sedes" class="mt-3">
            @foreach($sedes as $sede)
            <div class="form-check py-1">
                <input type="checkbox" name="sede_ids[]" value="{{ $sede->id }}" class="form-check-input"
                       id="sede_{{ $sede->id }}" {{ in_array($sede->id, old('sede_ids',[]))?'checked':'' }}>
                <label class="form-check-label" for="sede_{{ $sede->id }}" style="font-size:.875rem">
                    {{ $sede->nombre }}
                </label>
            </div>
            @endforeach
        </x-ui.card>
    </div>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Crear usuario</button>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>
