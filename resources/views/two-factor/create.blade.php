<x-layouts.app page-title="Activar 2FA">
<x-ui.page-header title="Activar autenticación de dos factores"
    :items="[['label'=>'2FA','url'=>route('two-factor.index')],['label'=>'Activar']]" />

<div class="row g-3 justify-content-center">
    <div class="col-md-6">
        <x-ui.card title="Escanea el código QR">
            <div class="text-center mb-4">
                <img src="{{ $qrUrl }}" alt="QR 2FA" class="img-fluid border rounded p-2" style="max-width:200px">
                <p class="text-muted mt-2" style="font-size:.875rem">
                    Escanea este código con tu app de autenticación<br>
                    (Google Authenticator, Authy, etc.)
                </p>
            </div>
            <div class="alert alert-secondary py-2 mb-3" style="font-size:.8rem">
                <strong>Clave secreta manual:</strong><br>
                <code style="font-size:.9rem;letter-spacing:.1em">{{ $secret }}</code>
            </div>
            <form method="POST" action="{{ route('two-factor.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-medium">Código de verificación <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control form-control-lg text-center font-monospace @error('code') is-invalid @enderror"
                           maxlength="6" pattern="[0-9]{6}" placeholder="000000" autofocus autocomplete="off" required>
                    <div class="form-text">Ingresa el código de 6 dígitos de tu app.</div>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="btn btn-success w-100">Confirmar y activar 2FA</button>
            </form>
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
