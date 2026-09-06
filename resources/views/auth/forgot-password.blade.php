<x-layouts.guest title="Recuperar contraseña">
<div class="login-card">
    <div class="text-center mb-4">
        <div class="login-brand-icon mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
            </svg>
        </div>
        <h5 class="fw-bold">Recuperar contraseña</h5>
        <p class="text-muted" style="font-size:.875rem">Ingresa tu correo y te enviaremos un enlace de recuperación.</p>
    </div>

    @if(session('status'))
    <div class="alert alert-success py-2 mb-3" style="font-size:.875rem">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-medium">Correo electrónico</label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" placeholder="usuario@escuela.mx" autofocus required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Enviar enlace de recuperación</button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none" style="font-size:.875rem">← Volver al login</a>
    </div>
</div>
</x-layouts.guest>
