{{-- ================================================
     Sidebar de navegación
     - Colapsable via Bootstrap JS + localStorage
     - Submenús via Bootstrap collapse
     - Menú filtrado por permisos del usuario
     ================================================ --}}

@php
    $user = auth()->user();
    $path = request()->path();
@endphp

{{-- Overlay mobile --}}
<div id="sidebar-overlay" class="sidebar-overlay" aria-hidden="true"></div>

<nav id="sidebar" aria-label="Navegación principal">

    {{-- Brand --}}
    <a href="{{ route('dashboard') }}" class="sidebar-brand text-decoration-none">
        <div class="sidebar-brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0 2 0V6.5a.5.5 0 0 0-.333-.47l-7.5-3Z"/>
                <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466 4.176 9.032Z"/>
            </svg>
        </div>
        <span class="sidebar-brand-name sidebar-label">{{ config('app.name') }}</span>
    </a>

    {{-- Menú --}}
    <div class="sidebar-nav">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="sidebar-item-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="sidebar-label">Dashboard</span>
        </a>

        {{-- ── CATÁLOGOS ─────────────────────────────── --}}
        @if(
            $user->puedeHacer('organizaciones.ver') ||
            $user->puedeHacer('escuelas.ver') ||
            $user->puedeHacer('sedes.ver') ||
            $user->puedeHacer('ciclos_escolares.ver') ||
            $user->puedeHacer('niveles_educativos.ver') ||
            $user->puedeHacer('grados.ver') ||
            $user->puedeHacer('grupos.ver') ||
            $user->puedeHacer('materias.ver')
        )
        <div class="mt-1">
            <button class="sidebar-item {{ request()->routeIs('organizaciones.*') || request()->routeIs('escuelas.*') || request()->routeIs('sedes.*') || request()->routeIs('ciclos.*') || request()->routeIs('niveles.*') || request()->routeIs('grados.*') || request()->routeIs('grupos.*') || request()->routeIs('materias.*') ? 'active' : '' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#nav-catalogos"
                    aria-expanded="{{ request()->routeIs('organizaciones.*') || request()->routeIs('escuelas.*') || request()->routeIs('sedes.*') || request()->routeIs('ciclos.*') || request()->routeIs('niveles.*') || request()->routeIs('grados.*') || request()->routeIs('grupos.*') || request()->routeIs('materias.*') ? 'true' : 'false' }}"
                    aria-controls="nav-catalogos">
                <svg class="sidebar-item-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="sidebar-label flex-grow-1 text-start">Catálogos</span>
                <svg class="sidebar-item-icon sidebar-label" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                     style="transition:transform .2s" id="nav-catalogos-chevron">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="collapse sidebar-submenu {{ request()->routeIs('organizaciones.*') || request()->routeIs('escuelas.*') || request()->routeIs('sedes.*') || request()->routeIs('ciclos.*') || request()->routeIs('niveles.*') || request()->routeIs('grados.*') || request()->routeIs('grupos.*') || request()->routeIs('materias.*') ? 'show' : '' }}"
                 id="nav-catalogos">

                @can('organizaciones.ver')
                <a href="{{ route('organizaciones.index') }}"
                   class="sidebar-item {{ request()->routeIs('organizaciones.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Organizaciones</span>
                </a>
                @endcan

                @can('escuelas.ver')
                <a href="{{ route('escuelas.index') }}"
                   class="sidebar-item {{ request()->routeIs('escuelas.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Escuelas</span>
                </a>
                @endcan

                @can('sedes.ver')
                <a href="{{ route('sedes.index') }}"
                   class="sidebar-item {{ request()->routeIs('sedes.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Sedes</span>
                </a>
                @endcan

                @can('ciclos_escolares.ver')
                <a href="{{ route('ciclos.index') }}"
                   class="sidebar-item {{ request()->routeIs('ciclos.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Ciclos Escolares</span>
                </a>
                @endcan

                @can('niveles_educativos.ver')
                <a href="{{ route('niveles.index') }}"
                   class="sidebar-item {{ request()->routeIs('niveles.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Niveles Educativos</span>
                </a>
                @endcan

                @can('grados.ver')
                <a href="{{ route('grados.index') }}"
                   class="sidebar-item {{ request()->routeIs('grados.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Grados</span>
                </a>
                @endcan

                @can('grupos.ver')
                <a href="{{ route('grupos.index') }}"
                   class="sidebar-item {{ request()->routeIs('grupos.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Grupos</span>
                </a>
                @endcan

                @can('materias.ver')
                <a href="{{ route('materias.index') }}"
                   class="sidebar-item {{ request()->routeIs('materias.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Materias</span>
                </a>
                @endcan

            </div>
        </div>
        @endif

        {{-- ── ACADÉMICO ─────────────────────────────── --}}
        @if(
            $user->puedeHacer('alumnos.ver') ||
            $user->puedeHacer('docentes.ver') ||
            $user->puedeHacer('inscripciones.ver') ||
            $user->puedeHacer('calificaciones.ver') ||
            $user->puedeHacer('asistencias.ver') ||
            $user->puedeHacer('horarios.ver')
        )
        <div class="mt-1">
            <button class="sidebar-item {{ request()->routeIs('alumnos.*') || request()->routeIs('docentes.*') || request()->routeIs('inscripciones.*') || request()->routeIs('calificaciones.*') || request()->routeIs('asistencias.*') || request()->routeIs('horarios.*') ? 'active' : '' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#nav-academico"
                    aria-expanded="{{ request()->routeIs('alumnos.*') || request()->routeIs('docentes.*') || request()->routeIs('inscripciones.*') || request()->routeIs('calificaciones.*') || request()->routeIs('asistencias.*') || request()->routeIs('horarios.*') ? 'true' : 'false' }}"
                    aria-controls="nav-academico">
                <svg class="sidebar-item-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                </svg>
                <span class="sidebar-label flex-grow-1 text-start">Académico</span>
                <svg class="sidebar-item-icon sidebar-label" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                     style="transition:transform .2s" id="nav-academico-chevron">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="collapse sidebar-submenu {{ request()->routeIs('alumnos.*') || request()->routeIs('docentes.*') || request()->routeIs('inscripciones.*') || request()->routeIs('calificaciones.*') || request()->routeIs('asistencias.*') || request()->routeIs('horarios.*') ? 'show' : '' }}"
                 id="nav-academico">

                @can('alumnos.ver')
                <a href="{{ route('alumnos.index') }}"
                   class="sidebar-item {{ request()->routeIs('alumnos.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Alumnos</span>
                </a>
                @endcan

                @can('docentes.ver')
                <a href="{{ route('docentes.index') }}"
                   class="sidebar-item {{ request()->routeIs('docentes.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Docentes</span>
                </a>
                @endcan

                @can('inscripciones.ver')
                <a href="{{ route('alumnos.inscripcion.index') }}"
                   class="sidebar-item {{ request()->routeIs('alumnos.inscripcion.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Inscripciones</span>
                </a>
                @endcan

                @can('calificaciones.ver')
                <a href="{{ route('calificaciones.index') }}"
                   class="sidebar-item {{ request()->routeIs('calificaciones.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Calificaciones</span>
                </a>
                @endcan

                @can('asistencias.ver')
                <a href="{{ route('asistencias.index') }}"
                   class="sidebar-item {{ request()->routeIs('asistencias.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Asistencias</span>
                </a>
                @endcan

                @can('horarios.ver')
                <a href="{{ route('horarios.index') }}"
                   class="sidebar-item {{ request()->routeIs('horarios.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Horarios</span>
                </a>
                @endcan

            </div>
        </div>
        @endif

        {{-- ── AUDITORÍA ─────────────────────────────── --}}
        @if($user->puedeHacer('auditoria.ver'))
        <div class="mt-1">
            <button class="sidebar-item {{ request()->routeIs('auditoria.*') ? 'active' : '' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#nav-auditoria"
                    aria-expanded="{{ request()->routeIs('auditoria.*') ? 'true' : 'false' }}"
                    aria-controls="nav-auditoria">
                <svg class="sidebar-item-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="sidebar-label flex-grow-1 text-start">Auditoría</span>
                <svg class="sidebar-item-icon sidebar-label" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                     style="transition:transform .2s" id="nav-auditoria-chevron">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="collapse sidebar-submenu {{ request()->routeIs('auditoria.*') ? 'show' : '' }}" id="nav-auditoria">
                <a href="{{ route('auditoria.index') }}"
                   class="sidebar-item {{ request()->routeIs('auditoria.index') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Registros</span>
                </a>
                <a href="{{ route('auditoria.accesos') }}"
                   class="sidebar-item {{ request()->routeIs('auditoria.accesos') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Accesos</span>
                </a>
                <a href="{{ route('auditoria.sesiones') }}"
                   class="sidebar-item {{ request()->routeIs('auditoria.sesiones') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Sesiones</span>
                </a>
                <a href="{{ route('auditoria.queries') }}"
                   class="sidebar-item {{ request()->routeIs('auditoria.queries') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Queries SQL</span>
                </a>
            </div>
        </div>
        @endif

        {{-- ── FINANZAS ─────────────────────────────── --}}
        @if($user->puedeHacer('finanzas.ver') || $user->puedeHacer('pagos.ver') || $user->puedeHacer('caja.ver'))
        <div class="mt-1">
            <button class="sidebar-item {{ request()->routeIs('finanzas.*') ? 'active' : '' }}"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#nav-finanzas"
                    aria-expanded="{{ request()->routeIs('finanzas.*') ? 'true' : 'false' }}"
                    aria-controls="nav-finanzas">
                <svg class="sidebar-item-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 8h20M2 8v10a2 2 0 002 2h16a2 2 0 002-2V8M2 8l4-4h12l4 4m-6 4h4"/>
                </svg>
                <span class="sidebar-label flex-grow-1 text-start">Finanzas</span>
                <svg class="sidebar-item-icon sidebar-label" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                     style="transition:transform .2s" id="nav-finanzas-chevron">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="collapse sidebar-submenu {{ request()->routeIs('finanzas.*') ? 'show' : '' }}" id="nav-finanzas">
                @can('cargos.ver')
                <a href="{{ route('finanzas.cargos.index') }}"
                   class="sidebar-item {{ request()->routeIs('finanzas.cargos.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Cargos</span>
                </a>
                @endcan

                @can('pagos.ver')
                <a href="{{ route('finanzas.pagos.index') }}"
                   class="sidebar-item {{ request()->routeIs('finanzas.pagos.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Pagos</span>
                </a>
                @endcan

                @can('caja.ver')
                <a href="{{ route('finanzas.caja.index') }}"
                   class="sidebar-item {{ request()->routeIs('finanzas.caja.*') ? 'active' : '' }}">
                    <span class="sidebar-item-icon d-flex align-items-center justify-content-center">
                        <span class="rounded-circle bg-secondary" style="width:6px;height:6px;opacity:.5"></span>
                    </span>
                    <span class="sidebar-label">Caja</span>
                </a>
                @endcan
            </div>
        </div>
        @endif

        {{-- ── CONFIGURACIÓN ────────────────────────── --}}
        @if($user->puedeHacer('configuracion.apariencia.editar'))
        <a href="{{ route('configuracion.apariencia') }}"
           class="sidebar-item mt-1 {{ request()->routeIs('configuracion.*') ? 'active' : '' }}">
            <svg class="sidebar-item-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="sidebar-label">Configuración</span>
        </a>
        @endif

    </div>{{-- /sidebar-nav --}}

    {{-- Footer: usuario + logout --}}
    <div class="sidebar-footer">
        <div class="sidebar-avatar flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->nombres ?? 'U', 0, 1)) }}
        </div>
        <div class="sidebar-user-info flex-grow-1 overflow-hidden">
            <div class="fw-semibold text-truncate" style="font-size:.8rem;color:var(--se-text)">
                {{ auth()->user()->nombre_completo }}
            </div>
            <div class="text-truncate" style="font-size:.7rem;color:var(--se-text-muted)">
                {{ auth()->user()->roles()->first()?->nombre ?? 'Sin rol' }}
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="sidebar-user-info">
            @csrf
            <button type="button" class="btn btn-link btn-sm p-0 text-secondary" data-bs-toggle="tooltip" data-bs-placement="right" title="Cerrar sesión"
                    onclick="this.closest('form').submit()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>

</nav>

{{-- Girar chevron cuando el collapse se abre/cierra --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Catálogos
        const catalogosCollapse = document.getElementById('nav-catalogos');
        const chevronCat = document.getElementById('nav-catalogos-chevron');
        if (catalogosCollapse && chevronCat) {
            catalogosCollapse.addEventListener('show.bs.collapse', () => chevronCat.style.transform = 'rotate(180deg)');
            catalogosCollapse.addEventListener('hide.bs.collapse', () => chevronCat.style.transform = 'rotate(0deg)');
            if (catalogosCollapse.classList.contains('show')) chevronCat.style.transform = 'rotate(180deg)';
        }

        // Académico
        const academicoCollapse = document.getElementById('nav-academico');
        const chevronAcad = document.getElementById('nav-academico-chevron');
        if (academicoCollapse && chevronAcad) {
            academicoCollapse.addEventListener('show.bs.collapse', () => chevronAcad.style.transform = 'rotate(180deg)');
            academicoCollapse.addEventListener('hide.bs.collapse', () => chevronAcad.style.transform = 'rotate(0deg)');
            if (academicoCollapse.classList.contains('show')) chevronAcad.style.transform = 'rotate(180deg)';
        }

        // Auditoría
        const auditCollapse = document.getElementById('nav-auditoria');
        const chevronAud = document.getElementById('nav-auditoria-chevron');
        if (auditCollapse && chevronAud) {
            auditCollapse.addEventListener('show.bs.collapse', () => chevronAud.style.transform = 'rotate(180deg)');
            auditCollapse.addEventListener('hide.bs.collapse', () => chevronAud.style.transform = 'rotate(0deg)');
            if (auditCollapse.classList.contains('show')) chevronAud.style.transform = 'rotate(180deg)';
        }

        // Finanzas
        const finanzasCollapse = document.getElementById('nav-finanzas');
        const chevronFin = document.getElementById('nav-finanzas-chevron');
        if (finanzasCollapse && chevronFin) {
            finanzasCollapse.addEventListener('show.bs.collapse', () => chevronFin.style.transform = 'rotate(180deg)');
            finanzasCollapse.addEventListener('hide.bs.collapse', () => chevronFin.style.transform = 'rotate(0deg)');
            if (finanzasCollapse.classList.contains('show')) chevronFin.style.transform = 'rotate(180deg)';
        }
    });
</script>