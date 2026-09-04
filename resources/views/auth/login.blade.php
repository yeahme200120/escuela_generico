<x-layouts.guest title="Iniciar sesión">

<div class="login-card">

    {{-- Brand --}}
    <div class="text-center mb-4">
        <div class="login-brand-icon mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0 2 0V6.5a.5.5 0 0 0-.333-.47l-7.5-3z"/>
                <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032z"/>
            </svg>
        </div>
        <h2 class="h5 fw-bold mb-1" style="color:var(--se-text)">{{ config('app.name') }}</h2>
        <p class="text-muted mb-0" style="font-size:.875rem">Ingresa con tus credenciales</p>
    </div>

    {{-- Error general --}}
    @if($errors->any())
    <div class="alert alert-danger d-flex align-items-start gap-2 py-2 px-3 mb-3" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="flex-shrink-0 mt-1" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </svg>
        <div style="font-size:.875rem">{{ $errors->first() }}</div>
    </div>
    @endif

    {{-- Formulario --}}
    <form method="POST" action="{{ route('login.store') }}" id="login-form" novalidate>
        @csrf

        {{-- ── Campos ocultos: geo + dispositivo ─────────────── --}}
        <input type="hidden" name="geo_latitude"  id="geo_latitude">
        <input type="hidden" name="geo_longitude" id="geo_longitude">
        <input type="hidden" name="geo_accuracy"  id="geo_accuracy">
        <input type="hidden" name="geo_altitude"  id="geo_altitude">
        <input type="hidden" name="geo_source"    id="geo_source" value="pending">
        <input type="hidden" name="device_id"     id="device_id">
        <input type="hidden" name="device_info"   id="device_info">

        {{-- Email / usuario --}}
        <div class="mb-3">
            <label for="email" class="form-label fw-medium" style="font-size:.875rem">
                Correo electrónico o usuario
            </label>
            <input
                type="text"
                id="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                autocomplete="username"
                placeholder="usuario@escuela.mx"
                autofocus
                required
            >
        </div>

        {{-- Contraseña --}}
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label fw-medium mb-0" style="font-size:.875rem">
                    Contraseña
                </label>
                {{-- Recuperar contraseña (fase futura) --}}
                {{-- <a href="#" class="text-decoration-none" style="font-size:.8rem">¿Olvidaste tu contraseña?</a> --}}
            </div>
            <div class="input-group">
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    required
                >
                <button class="btn btn-outline-secondary" type="button" id="toggle-password"
                        aria-label="Mostrar/ocultar contraseña"
                        title="Mostrar contraseña">
                    {{-- ojo abierto --}}
                    <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zm-8 4.5a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9z"/>
                        <path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                    </svg>
                    {{-- ojo cerrado --}}
                    <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display:none">
                        <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                        <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                        <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709z"/>
                        <path fill-rule="evenodd" d="M13.646 14.354l-12-12 .708-.708 12 12-.708.708z"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Recordarme --}}
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                       {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember" style="font-size:.875rem">
                    Mantener sesión iniciada
                </label>
            </div>
        </div>

        {{-- Indicador de geolocalización --}}
        <div id="geo-status" class="d-flex align-items-center gap-2 mb-3 px-3 py-2 rounded"
             style="background:#f8fafc;border:1px solid var(--se-border);font-size:.8rem;color:var(--se-text-muted)">
            <div id="geo-spinner" class="spinner-border spinner-border-sm text-secondary" role="status" style="width:14px;height:14px;border-width:2px">
                <span class="visually-hidden">Cargando…</span>
            </div>
            <svg id="geo-ok" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#16a34a" viewBox="0 0 16 16" style="display:none;flex-shrink:0">
                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
            </svg>
            <svg id="geo-denied" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#d97706" viewBox="0 0 16 16" style="display:none;flex-shrink:0">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <span id="geo-msg">Verificando ubicación…</span>
        </div>

        {{-- Botón submit --}}
        <button type="submit" id="btn-login"
                class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2"
                style="height:42px">
            <span id="btn-text" class="d-flex align-items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                    <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                </svg>
                Iniciar sesión
            </span>
            <span id="btn-loading" class="d-none d-flex align-items-center gap-2">
                <span class="spinner-border spinner-border-sm" role="status" style="width:14px;height:14px;border-width:2px"></span>
                Verificando…
            </span>
        </button>

    </form>

    {{-- Nota de privacidad --}}
    <p class="text-center text-muted mt-3 mb-0" style="font-size:.75rem">
        Conexión segura. La ubicación se registra únicamente para auditoría de accesos.
    </p>

</div>{{-- /.login-card --}}

{{-- ============================================================
     JS de captura de geo + dispositivo
     Se ejecuta inmediatamente al cargar la página.
     Los datos se almacenan en inputs hidden y se envían con el form.
     ============================================================ --}}
<script>
(async function () {
    // ── Elementos del DOM ────────────────────────────────────────
    const geoMsg     = document.getElementById('geo-msg');
    const geoSpinner = document.getElementById('geo-spinner');
    const geoOk      = document.getElementById('geo-ok');
    const geoDenied  = document.getElementById('geo-denied');
    const geoStatus  = document.getElementById('geo-status');
    const form       = document.getElementById('login-form');
    const btnText    = document.getElementById('btn-text');
    const btnLoading = document.getElementById('btn-loading');

    // ── Toggle contraseña ────────────────────────────────────────
    document.getElementById('toggle-password')?.addEventListener('click', function () {
        const pwd      = document.getElementById('password');
        const eyeOpen  = document.getElementById('eye-open');
        const eyeClosed= document.getElementById('eye-closed');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            eyeOpen.style.display  = 'none';
            eyeClosed.style.display= 'inline';
        } else {
            pwd.type = 'password';
            eyeOpen.style.display  = 'inline';
            eyeClosed.style.display= 'none';
        }
    });

    // ── Loading en submit ────────────────────────────────────────
    form?.addEventListener('submit', function () {
        btnText.classList.add('d-none');
        btnLoading.classList.remove('d-none');
        document.getElementById('btn-login').disabled = true;
    });

    // ── Captura de dispositivo ───────────────────────────────────
    async function captureDevice() {
        try {
            const info = window.DeviceInfo?.get() ?? {};
            // Device ID via SHA-256 del fingerprint
            const deviceId = await window.DeviceInfo?.getId() ?? '';
            document.getElementById('device_id').value   = deviceId;
            document.getElementById('device_info').value = JSON.stringify(info);
        } catch(e) {
            console.warn('DeviceInfo capture failed:', e);
        }
    }

    // ── Captura de geolocalización ───────────────────────────────
    async function captureGeo() {
        if (!window.GeoCapture) {
            geoMsg.textContent = 'Módulo de ubicación no disponible.';
            return;
        }

        geoMsg.textContent = 'Obteniendo ubicación…';

        const geo = await window.GeoCapture.getPosition({ timeout: 8000 });

        // Llenar campos ocultos
        document.getElementById('geo_latitude').value  = geo.latitude  ?? '';
        document.getElementById('geo_longitude').value = geo.longitude ?? '';
        document.getElementById('geo_accuracy').value  = geo.accuracy  ?? '';
        document.getElementById('geo_altitude').value  = geo.altitude  ?? '';
        document.getElementById('geo_source').value    = geo.source    ?? 'unknown';

        // Actualizar indicador visual
        geoSpinner.style.display = 'none';

        if (geo.latitude !== null) {
            geoOk.style.display = 'inline';
            geoMsg.textContent  = `Ubicación capturada (±${Math.round(geo.accuracy ?? 0)} m)`;
            geoStatus.style.borderColor = '#bbf7d0';
            geoStatus.style.background  = '#f0fdf4';
            geoMsg.style.color = '#166534';
        } else {
            geoDenied.style.display = 'inline';
            geoMsg.textContent      = 'Sin ubicación — el acceso se registrará sin coordenadas.';
            geoStatus.style.borderColor = '#fde68a';
            geoStatus.style.background  = '#fffbeb';
            geoMsg.style.color = '#92400e';
        }
    }

    // ── Ejecutar en paralelo ─────────────────────────────────────
    await Promise.all([captureGeo(), captureDevice()]);
})();
</script>

</x-layouts.guest>
