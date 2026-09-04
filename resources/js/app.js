/**
 * Sistema Escolar — app.js
 * Bootstrap 5 + GeoCapture + DeviceInfo + Sidebar toggle
 */

// ── Bootstrap 5 completo (JS + Popper)
import * as bootstrap from 'bootstrap';

// ── Axios (configurado en bootstrap.js de Laravel)
import './bootstrap';

// ============================================================
// GeoCapture — Obtiene ubicación GPS del dispositivo
// ============================================================
window.GeoCapture = {
    /**
     * Solicita la posición actual del dispositivo.
     * Resuelve siempre (nunca rechaza) para no bloquear el flujo.
     * @returns {Promise<{latitude, longitude, accuracy, altitude, speed, source, error}>}
     */
    getPosition(options = {}) {
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                resolve({ latitude: null, longitude: null, accuracy: null, source: 'unavailable' });
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (pos) => resolve({
                    latitude:  pos.coords.latitude,
                    longitude: pos.coords.longitude,
                    accuracy:  pos.coords.accuracy,
                    altitude:  pos.coords.altitude,
                    speed:     pos.coords.speed,
                    heading:   pos.coords.heading,
                    source:    'gps',
                }),
                (err) => resolve({
                    latitude: null, longitude: null, accuracy: null,
                    source: 'denied', error: err.message,
                }),
                {
                    enableHighAccuracy: true,
                    timeout:     options.timeout     ?? 8000,
                    maximumAge:  options.maximumAge  ?? 0,
                }
            );
        });
    },
};

// ============================================================
// DeviceInfo — Fingerprint del dispositivo
// ============================================================
window.DeviceInfo = {
    get() {
        return {
            screen_width:  window.screen.width,
            screen_height: window.screen.height,
            timezone:      Intl.DateTimeFormat().resolvedOptions().timeZone,
            language:      navigator.language,
            platform:      navigator.platform,
            user_agent:    navigator.userAgent,
            color_depth:   window.screen.colorDepth,
            pixel_ratio:   window.devicePixelRatio,
            touch:         ('ontouchstart' in window),
            cookies:       navigator.cookieEnabled,
        };
    },
    /**
     * Genera un device_id estable basado en SHA-256 del fingerprint.
     * @returns {Promise<string>}
     */
    async getId() {
        const info = this.get();
        const raw  = [
            info.user_agent,
            info.screen_width,
            info.screen_height,
            info.timezone,
            info.platform,
            info.color_depth,
        ].join('|');

        const buf  = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(raw));
        return Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join('');
    },
};

// ============================================================
// Sidebar toggle
// ============================================================
document.addEventListener('DOMContentLoaded', () => {

    const sidebar      = document.getElementById('sidebar');
    const mainContent  = document.getElementById('main-content');
    const toggleBtn    = document.getElementById('sidebar-toggle');
    const overlay      = document.getElementById('sidebar-overlay');

    if (!sidebar) return;

    // Estado guardado en localStorage
    const STORAGE_KEY = 'se_sidebar_collapsed';
    const isCollapsed = localStorage.getItem(STORAGE_KEY) === '1';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent?.classList.add('sidebar-collapsed');
    }

    // Toggle desktop
    toggleBtn?.addEventListener('click', () => {
        const collapsed = sidebar.classList.toggle('collapsed');
        mainContent?.classList.toggle('sidebar-collapsed', collapsed);
        localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
    });

    // Toggle mobile
    const mobileToggle = document.getElementById('sidebar-mobile-toggle');
    mobileToggle?.addEventListener('click', () => {
        sidebar.classList.toggle('mobile-open');
        overlay?.classList.toggle('active');
    });

    overlay?.addEventListener('click', () => {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    });

    // ── Submenús acordeón (Bootstrap collapse ya lo maneja) ──
    // Solo necesitamos marcar el ítem activo del submenú
    const currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar-item[data-path]').forEach(el => {
        const path = el.getAttribute('data-path');
        if (path && currentPath.startsWith(path)) {
            el.classList.add('active');
            // Abrir el collapse padre si existe
            const collapse = el.closest('.collapse');
            if (collapse) {
                collapse.classList.add('show');
                const trigger = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
                trigger?.setAttribute('aria-expanded', 'true');
                trigger?.classList.remove('collapsed');
            }
        }
    });

    // ── Flash messages auto-dismiss ──────────────────────────
    document.querySelectorAll('.alert-dismissible.auto-dismiss').forEach(el => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert?.close();
        }, 4000);
    });

    // ── Tooltips ─────────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });

    // ── Confirmaciones de formularios destructivos ────────────
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', (e) => {
            const msg = form.getAttribute('data-confirm') || '¿Confirmar acción?';
            if (!confirm(msg)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });

    // ── SQL expandir en QueryLog ─────────────────────────────
    document.querySelectorAll('.sql-preview[data-full-sql]').forEach(el => {
        el.addEventListener('click', () => {
            const modalId = 'sqlModal';
            let modal = document.getElementById(modalId);
            if (!modal) {
                modal = document.createElement('div');
                modal.id = modalId;
                modal.innerHTML = `
                    <div class="modal fade" id="${modalId}Inner" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">SQL completo</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-0">
                                    <pre class="m-0 p-3" style="font-size:.8rem;background:#0f172a;color:#e2e8f0;border-radius:0 0 6px 6px;overflow-x:auto;white-space:pre-wrap;" id="sqlModalContent"></pre>
                                </div>
                            </div>
                        </div>
                    </div>`;
                document.body.appendChild(modal);
            }
            document.getElementById('sqlModalContent').textContent = el.getAttribute('data-full-sql');
            bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId + 'Inner')).show();
        });
    });
});
