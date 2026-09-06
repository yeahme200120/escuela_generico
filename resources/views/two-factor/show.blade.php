{{-- Vista de challenge 2FA durante el login --}}
<x-layouts.guest title="Verificación 2FA">
<div class="login-card">
    <div class="text-center mb-4">
        <div class="login-brand-icon mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
            </svg>
        </div>
        <h5 class="fw-bold">Verificación en dos pasos</h5>
        <p class="text-muted" style="font-size:.875rem">Ingresa el código de tu aplicación de autenticación.</p>
    </div>

    @if(session('error'))
    <div class="alert alert-danger py-2 mb-3" style="font-size:.875rem">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf
        <div class="mb-4">
            <input type="text" name="code"
                   class="form-control form-control-lg text-center font-monospace @error('code') is-invalid @enderror"
                   maxlength="6" pattern="[0-9]{6}" placeholder="000000"
                   autofocus autocomplete="one-time-code" required>
            @error('code')<div class="invalid-feedback text-center">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Verificar</button>
    </form>
</div>
</x-layouts.guest>
