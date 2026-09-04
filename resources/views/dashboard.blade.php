<x-layouts.app page-title="Dashboard">

    {{-- Encabezado de página --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color:var(--se-text)">Dashboard</h4>
            <p class="mb-0 text-muted" style="font-size:.875rem">
                Bienvenido, <strong>{{ auth()->user()->nombre_completo }}</strong>
            </p>
        </div>
        <span class="badge text-bg-secondary" style="font-size:.75rem">
            Fase 1 — Núcleo
        </span>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                        <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                        <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                    </svg>
                </div>
                <div class="stat-value">—</div>
                <div class="stat-label">Alumnos activos</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M3 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    </svg>
                </div>
                <div class="stat-value">—</div>
                <div class="stat-label">Docentes</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6zm3.5 1a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 2a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zm0 2a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2z"/>
                        <path d="M2 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v1H2V4z"/>
                    </svg>
                </div>
                <div class="stat-value">—</div>
                <div class="stat-label">Grupos activos</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M8 1a5 5 0 1 0 0 10A5 5 0 0 0 8 1zM2 8a6 6 0 1 1 12 0A6 6 0 0 1 2 8z"/>
                        <path d="M8 3.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V8H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9H6a.5.5 0 0 1 0-1h1.5V6H6a.5.5 0 0 1 0-1h1.5V4a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                </div>
                <div class="stat-value">—</div>
                <div class="stat-label">Sesiones activas</div>
            </div>
        </div>
    </div>

    {{-- Info de la fase --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="card-title fw-semibold">Sistema Escolar Multisede — Fase 1 completada</h6>
            <p class="card-text text-muted mb-3" style="font-size:.875rem">
                El núcleo del sistema está operativo. Las migraciones, modelos, RBAC,
                trazabilidad completa (geo + dispositivo + queries) y autenticación están implementados.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge text-bg-success">✓ Base de datos (25+ tablas)</span>
                <span class="badge text-bg-success">✓ 20 modelos Eloquent</span>
                <span class="badge text-bg-success">✓ RBAC multirol + multisede</span>
                <span class="badge text-bg-success">✓ Auditoría inmutable</span>
                <span class="badge text-bg-success">✓ Trazabilidad geo + dispositivo</span>
                <span class="badge text-bg-success">✓ Query logging</span>
                <span class="badge text-bg-warning text-dark">⏳ Módulos académicos</span>
                <span class="badge text-bg-warning text-dark">⏳ Finanzas</span>
                <span class="badge text-bg-secondary">🔲 Python / FastAPI</span>
            </div>
        </div>
    </div>

</x-layouts.app>
