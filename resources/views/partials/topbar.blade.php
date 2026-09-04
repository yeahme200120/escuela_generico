{{-- Topbar principal --}}
<header id="topbar">

    {{-- Toggle sidebar desktop --}}
    <button id="sidebar-toggle"
            class="btn btn-link btn-sm text-secondary p-1 me-1 d-none d-md-inline-flex align-items-center"
            aria-label="Alternar menú lateral"
            data-bs-toggle="tooltip" data-bs-placement="bottom" title="Alternar menú">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Toggle sidebar mobile --}}
    <button id="sidebar-mobile-toggle"
            class="btn btn-link btn-sm text-secondary p-1 me-1 d-inline-flex d-md-none align-items-center"
            aria-label="Abrir menú">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Título de página (breadcrumb) --}}
    <div class="flex-grow-1 d-flex align-items-center gap-2">
        @isset($pageTitle)
            <span class="fw-semibold" style="font-size:.875rem;color:var(--se-text)">{{ $pageTitle }}</span>
        @endisset
        @isset($breadcrumb)
            <nav aria-label="breadcrumb" class="d-none d-lg-block">
                <ol class="breadcrumb mb-0" style="font-size:.8rem">
                    {{ $breadcrumb }}
                </ol>
            </nav>
        @endisset
    </div>

    {{-- Sede activa --}}
    @php $sedeNombre = app(\App\Support\RequestContext::class)->getSedeNombre(); @endphp
    @if($sedeNombre)
        <span class="badge rounded-pill text-bg-info d-none d-sm-inline">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
            </svg>
            {{ $sedeNombre }}
        </span>
    @endif

    {{-- Request ID (solo en debug) --}}
    @if(config('app.debug'))
        <span class="request-id-badge d-none d-xl-inline"
              title="Request ID — útil para correlacionar logs">
            {{ app(\App\Support\RequestContext::class)->getRequestId() }}
        </span>
    @endif

    {{-- Menú usuario --}}
    <div class="dropdown">
        <button class="btn btn-link btn-sm p-0 d-flex align-items-center gap-2 text-decoration-none"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="Menú de usuario">
            <div class="sidebar-avatar" style="width:30px;height:30px;font-size:.75rem">
                {{ strtoupper(substr(auth()->user()->nombres ?? 'U', 0, 1)) }}
            </div>
            <span class="d-none d-md-inline fw-medium" style="font-size:.8rem;color:var(--se-text)">
                {{ auth()->user()->nombres }}
            </span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-secondary">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width:200px">
            <li>
                <div class="dropdown-item-text py-2 border-bottom">
                    <div class="fw-semibold" style="font-size:.875rem">{{ auth()->user()->nombre_completo }}</div>
                    <div class="text-muted" style="font-size:.75rem">{{ auth()->user()->email }}</div>
                    <span class="badge text-bg-secondary mt-1" style="font-size:.65rem">
                        {{ auth()->user()->roles()->first()?->nombre ?? 'Sin rol' }}
                    </span>
                </div>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Cerrar sesión
                    </button>
                </form>
            </li>
        </ul>
    </div>

</header>
