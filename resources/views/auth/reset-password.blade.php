<x-layouts.guest title="Nueva contraseña">
<div class="login-card">
    <div class="text-center mb-4">
        <h5 class="fw-bold">Crear nueva contraseña</h5>
        <p class="text-muted" style="font-size:.875rem">Ingresa tu nueva contraseña para recuperar el acceso.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label fw-medium">Correo electrónico</label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', request('email')) }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label class="form-label fw-medium">Nueva contraseña</label>
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Mínimo 8 caracteres" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="form-label fw-medium">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Restablecer contraseña</button>
    </form>
</div>
</x-layouts.guest>
