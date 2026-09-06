<x-layouts.app page-title="Autenticación de dos factores">
<x-ui.page-header title="Autenticación de dos factores (2FA)"
    subtitle="Añade una capa de seguridad adicional a tu cuenta. §63" />

<div class="row g-3 justify-content-center">
    <div class="col-md-6">
        <x-ui.card title="Estado actual">
            <div class="d-flex align-items-center gap-3 mb-4">
                @if(auth()->user()->two_factor_enabled)
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-success" viewBox="0 0 16 16">
                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-semibold text-success">2FA Activo</div>
                        <small class="text-muted">Tu cuenta está protegida con autenticación de dos factores.</small>
                    </div>
                @else
                    <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="text-warning" viewBox="0 0 16 16">
                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-semibold text-warning">2FA Inactivo</div>
                        <small class="text-muted">Tu cuenta no tiene protección de segundo factor.</small>
                    </div>
                @endif
            </div>

            @if(auth()->user()->two_factor_enabled)
                <x-ui.confirm id="confirm-disable-2fa"
                    title="¿Desactivar 2FA?"
                    message="Tu cuenta quedará menos protegida. Ingresa tu contraseña para confirmar."
                    :action="route('two-factor.destroy')"
                    method="DELETE"
                    label="Desactivar 2FA"
                    type="danger"
                    :motivo="false">
                    <x-slot name="trigger">
                        <button type="button" class="btn btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#confirm-disable-2fa">
                            Desactivar 2FA
                        </button>
                    </x-slot>
                </x-ui.confirm>
            @else
                <a href="{{ route('two-factor.create') }}" class="btn btn-success">
                    Activar autenticación de dos factores
                </a>
            @endif
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
