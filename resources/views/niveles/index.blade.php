@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Niveles Educativos" 
        subtitle="Gestión de niveles (preescolar, primaria, secundaria, etc.)"
        :actions="[
            ['label' => 'Nuevo Nivel', 'route' => route('niveles.create'), 'icon' => 'plus']
        ]"
    />

    <x-ui.card>
        <x-slot:header>
            <form method="GET" action="{{ route('niveles.index') }}" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o clave..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="activo" class="form-control">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    <a href="{{ route('niveles.index') }}" class="btn btn-secondary">Limpiar</a>
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
                        <th>Orden</th>
                        <th>Organización</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($niveles as $nivel)
                    <tr>
                        <td>{{ $nivel->id }}</td>
                        <td>{{ $nivel->nombre }}</td>
                        <td>{{ $nivel->clave ?? '—' }}</td>
                        <td>{{ $nivel->orden ?? '—' }}</td>
                        <td>{{ $nivel->organizacion->nombre ?? 'N/A' }}</td>
                        <td>
                            <x-ui.badge :type="$nivel->activo ? 'success' : 'danger'">
                                {{ $nivel->activo ? 'Activo' : 'Inactivo' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('niveles.show', $nivel) }}" class="btn btn-sm btn-outline-info">Ver</a>
                            <a href="{{ route('niveles.edit', $nivel) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            <form action="{{ route('niveles.destroy', $nivel) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este nivel?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4"><x-ui.empty-state message="No hay niveles educativos registrados" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>{{ $niveles->links() }}</x-slot:footer>
    </x-ui.card>
</div>
@endsection