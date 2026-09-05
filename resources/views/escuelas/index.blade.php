@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Escuelas" 
        subtitle="Gestión de instituciones educativas"
        :actions="[
            ['label' => 'Nueva Escuela', 'route' => route('escuelas.create'), 'icon' => 'plus']
        ]"
    />

    <x-ui.card>
        <x-slot:header>
            <form method="GET" action="{{ route('escuelas.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, clave o ciudad..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="activa" class="form-control">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activa') == '1' ? 'selected' : '' }}>Activas</option>
                        <option value="0" {{ request('activa') == '0' ? 'selected' : '' }}>Inactivas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    <a href="{{ route('escuelas.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </x-slot:header>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Clave</th>
                        <th>Organización</th>
                        <th>Ciudad</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($escuelas as $escuela)
                    <tr>
                        <td>{{ $escuela->id }}</td>
                        <td>{{ $escuela->nombre }}</td>
                        <td>{{ $escuela->clave ?? '—' }}</td>
                        <td>{{ $escuela->organizacion->nombre ?? 'N/A' }}</td>
                        <td>{{ $escuela->ciudad ?? '—' }}</td>
                        <td>
                            <x-ui.badge :type="$escuela->activa ? 'success' : 'danger'">
                                {{ $escuela->activa ? 'Activa' : 'Inactiva' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('escuelas.show', $escuela) }}" class="btn btn-sm btn-outline-info">Ver</a>
                            <a href="{{ route('escuelas.edit', $escuela) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            <form action="{{ route('escuelas.destroy', $escuela) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta escuela?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4"><x-ui.empty-state message="No hay escuelas registradas" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>{{ $escuelas->links() }}</x-slot:footer>
    </x-ui.card>
</div>
@endsection