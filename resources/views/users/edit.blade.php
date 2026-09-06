<x-layouts.app page-title="Editar usuario">
<x-ui.page-header title="Editar: {{ $user->nombre_completo }}"
    :items="[['label'=>'Usuarios','url'=>route('users.index')],['label'=>$user->nombre_completo,'url'=>route('users.show',$user)],['label'=>'Editar']]" />

<form method="POST" action="{{ route('users.update',$user) }}">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Datos personales">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror"
                           value="{{ old('nombres',$user->nombres) }}" required>
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Apellido paterno <span class="text-danger">*</span></label>
                    <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror"
                           value="{{ old('apellido_paterno',$user->apellido_paterno) }}" required>
                    @error('apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Apellido materno</label>
                    <input type="text" name="apellido_materno" class="form-control"
                           value="{{ old('apellido_materno',$user->apellido_materno) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Correo <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email',$user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nueva contraseña <span class="text-muted fw-normal">(dejar vacío = sin cambio)</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="activo" value="1" class="form-check-input" id="activo"
                               {{ old('activo',$user->activo)?'checked':'' }}>
                        <label class="form-check-label" for="activo">Usuario activo</label>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
    <div class="col-md-4">
        <x-ui.card title="Información de acceso">
            <dl class="mb-0" style="font-size:.875rem">
                <dt class="text-muted">Último acceso</dt>
                <dd>{{ $user->ultimo_acceso_at?->format('d/m/Y H:i') ?? 'Nunca' }}</dd>
                <dt class="text-muted">Último IP</dt>
                <dd>{{ $user->ultimo_ip ?? '—' }}</dd>
                <dt class="text-muted">Verificado</dt>
                <dd>{{ $user->email_verified_at ? '✅' : '❌' }}</dd>
                <dt class="text-muted">2FA</dt>
                <dd>{{ $user->two_factor_enabled ? '✅ Activo' : '❌ Inactivo' }}</dd>
            </dl>
        </x-ui.card>
    </div>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Actualizar usuario</button>
    <a href="{{ route('users.show',$user) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>
