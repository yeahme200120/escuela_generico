<x-layouts.app page-title="Apariencia">

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Apariencia del sistema</h4>
        <p class="text-muted mb-0" style="font-size:.875rem">
            Personaliza la identidad visual de la plataforma. Solo visible para superadmin.
        </p>
    </div>
</div>

<form method="POST" action="{{ route('configuracion.apariencia.update') }}" id="form-tema">
    @csrf

    <div class="row g-4">

        {{-- ── Colores ──────────────────────────────────────── --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent fw-semibold border-bottom">
                    Colores del tema
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                        $colores = [
                            'primary'    => ['label' => 'Color primario',    'desc' => 'Botones, links, sidebar activo'],
                            'secondary'  => ['label' => 'Color secundario',  'desc' => 'Textos auxiliares, badges'],
                            'success'    => ['label' => 'Éxito',             'desc' => 'Alertas positivas, badges OK'],
                            'warning'    => ['label' => 'Advertencia',       'desc' => 'Alertas de atención'],
                            'danger'     => ['label' => 'Peligro',           'desc' => 'Errores, eliminaciones'],
                            'info'       => ['label' => 'Informativo',       'desc' => 'Badges de info, sedes'],
                            'background' => ['label' => 'Fondo de página',   'desc' => 'Color de fondo general'],
                            'surface'    => ['label' => 'Superficie',        'desc' => 'Cards, sidebar, topbar'],
                            'text'       => ['label' => 'Texto principal',   'desc' => 'Color del texto base'],
                        ];
                        @endphp

                        @foreach($colores as $key => $meta)
                        <div class="col-12 col-sm-6 col-xl-4">
                            <label for="color_{{ $key }}" class="form-label fw-medium mb-1" style="font-size:.8rem">
                                {{ $meta['label'] }}
                                <span class="text-muted fw-normal">— {{ $meta['desc'] }}</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="color"
                                       id="color_{{ $key }}"
                                       class="form-control form-control-color"
                                       style="width:44px;padding:2px 4px;cursor:pointer"
                                       value="{{ $tema['theme.'.$key] ?? '#000000' }}"
                                       oninput="document.getElementById('hex_{{ $key }}').value = this.value">
                                <input type="text"
                                       id="hex_{{ $key }}"
                                       name="{{ $key }}"
                                       class="form-control font-monospace @error($key) is-invalid @enderror"
                                       value="{{ old($key, $tema['theme.'.$key] ?? '') }}"
                                       placeholder="#2563eb"
                                       maxlength="7"
                                       pattern="#[0-9a-fA-F]{6}"
                                       oninput="syncColor('{{ $key }}', this.value)">
                                @error($key)
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Preview en vivo ──────────────────────────────── --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-semibold border-bottom">
                    Vista previa
                </div>
                <div class="card-body" id="preview-panel" style="background:var(--preview-bg,#f1f5f9);border-radius:0 0 8px 8px">
                    {{-- Simula un mini sidebar --}}
                    <div class="rounded-3 overflow-hidden border mb-3" style="font-size:.75rem">
                        <div id="prev-sidebar" class="p-2" style="background:var(--preview-surface,#fff)">
                            <div id="prev-brand" class="rounded px-2 py-1 mb-2 d-inline-flex align-items-center gap-2"
                                 style="background:var(--preview-primary,#2563eb);color:#fff;font-weight:700;font-size:.7rem">
                                ■ Sistema Escolar
                            </div>
                            <div class="rounded px-2 py-1 mb-1"
                                 style="background:color-mix(in srgb,var(--preview-primary,#2563eb) 15%,transparent);color:var(--preview-primary,#2563eb);font-size:.7rem">
                                ▪ Dashboard
                            </div>
                            <div class="rounded px-2 py-1" style="color:#64748b;font-size:.7rem">▪ Auditoría</div>
                        </div>
                        <div class="p-2" style="background:var(--preview-bg,#f1f5f9)">
                            <div class="d-flex gap-1 mb-2">
                                <span id="prev-badge-success" class="badge" style="background:var(--preview-success,#16a34a)">Activo</span>
                                <span id="prev-badge-danger"  class="badge" style="background:var(--preview-danger,#dc2626)">Error</span>
                                <span id="prev-badge-warning" class="badge text-dark" style="background:var(--preview-warning,#d97706)">Atención</span>
                                <span id="prev-badge-info"    class="badge" style="background:var(--preview-info,#0891b2)">Info</span>
                            </div>
                            <div class="rounded p-2 mb-2 border" style="background:var(--preview-surface,#fff);font-size:.7rem;color:var(--preview-text,#0f172a)">
                                Card de ejemplo con <strong>texto principal</strong>
                            </div>
                            <button type="button" class="btn btn-sm w-100"
                                    style="background:var(--preview-primary,#2563eb);color:#fff;font-size:.7rem">
                                Botón primario
                            </button>
                        </div>
                    </div>

                    {{-- Restablecer defaults --}}
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="btn-reset-theme">
                        Restablecer valores por defecto
                    </button>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}

    {{-- Botones de acción --}}
    <div class="d-flex gap-2 justify-content-end mt-4">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
            </svg>
            Guardar tema
        </button>
    </div>

</form>

@push('scripts')
<script>
// Defaults para poder resetear
const DEFAULTS = @json($defaults);

// Sincroniza color picker ↔ input text
function syncColor(key, hexValue) {
    const picker = document.getElementById('color_' + key);
    const input  = document.getElementById('hex_' + key);
    if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(hexValue)) {
        picker.value = hexValue;
    }
    updatePreview(key, hexValue);
}

// Actualiza variables CSS del panel de preview
function updatePreview(key, value) {
    const map = {
        primary: '--preview-primary', secondary: '--preview-secondary',
        success: '--preview-success', warning: '--preview-warning',
        danger:  '--preview-danger',  info:    '--preview-info',
        background: '--preview-bg',   surface: '--preview-surface',
        text:    '--preview-text',
    };
    if (map[key]) {
        document.getElementById('preview-panel').style.setProperty(map[key], value);
    }
}

// Inicializar preview con valores actuales
document.addEventListener('DOMContentLoaded', () => {
    ['primary','secondary','success','warning','danger','info','background','surface','text'].forEach(key => {
        const val = document.getElementById('hex_' + key)?.value;
        if (val) updatePreview(key, val);
    });

    // Restablecer defaults
    document.getElementById('btn-reset-theme')?.addEventListener('click', () => {
        Object.entries(DEFAULTS).forEach(([dotKey, value]) => {
            const key = dotKey.replace('theme.', '');
            const picker = document.getElementById('color_' + key);
            const input  = document.getElementById('hex_'   + key);
            if (picker && value) picker.value = value;
            if (input  && value) input.value  = value;
            updatePreview(key, value);
        });
    });
});
</script>
@endpush

</x-layouts.app>
