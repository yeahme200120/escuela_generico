@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Sedes" 
        subtitle="Gestión de campus y planteles"
        :actions="[
            ['label' => 'Nueva Sede', 'route' => route('sedes.create'), 'icon' => 'plus']
        ]"
    />

    <x-ui.card>
        <x-slot:header>
            <form method="GET" action="{{ route('sedes.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, clave o ciudad..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Filtrar</button>
                    <a href="{{ route('sedes.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </x-slot:header>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Clave</th>
                        <th>Escuela</th>
                        <th>Ciudad</th>
                        <th>Geocerca</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sedes as $sede)
                    <tr>
                        <td>{{ $sede->id }}</td>
                        <td>{{ $sede->nombre }}</td>
                        <td>{{ $sede->clave ?? '—' }}</td>
                        <td>{{ $sede->escuela->nombre ?? 'N/A' }}</td>
                        <td>{{ $sede->ciudad ?? '—' }}</td>
                        <td>
                            @if($sede->geocerca_activa && $sede->latitud && $sede->longitud)
                                <span class="badge bg-info" title="Radio: {{ $sede->radio_geocerca_metros }}m">📍 Activa</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <x-ui.badge :type="$sede->activa ? 'success' : 'danger'">
                                {{ $sede->activa ? 'Activa' : 'Inactiva' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('sedes.show', $sede) }}" class="btn btn-sm btn-outline-info">Ver</a>
                            <a href="{{ route('sedes.edit', $sede) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            <form action="{{ route('sedes.destroy', $sede) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta sede? Se desactivará.')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <x-ui.empty-state message="No hay sedes registradas" />
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>
            {{ $sedes->links() }}
        </x-slot:footer>
    </x-ui.card>
</div>
@endsection