{{--
    Filter bar — barra de filtros GET para tablas paginadas.
    Uso:
        <x-ui.filter-bar :action="route('auditoria.index')">
            <x-slot name="fields"> ... inputs ... </x-slot>
        </x-ui.filter-bar>
--}}
@props(['action' => '', 'method' => 'GET'])
<form method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}"
      action="{{ $action }}"
      class="card border-0 shadow-sm mb-4">
    @if(strtoupper($method) !== 'GET') @csrf @endif
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            {{ $fields }}
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.44 1.406a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/>
                    </svg>
                    Filtrar
                </button>
                <a href="{{ $action }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
            </div>
        </div>
    </div>
</form>
