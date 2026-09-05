@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Ciclos Escolares" 
        subtitle="Gestión de periodos escolares"
        :actions="[
            ['label' => 'Nuevo Ciclo', 'route' => route('ciclos-escolares.create'), 'icon' => 'plus']
        ]"
    />

    <x-ui.card>
        <x-slot:header>
            <form method="GET" action="{{ route('ciclos-escolares.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="activo" class="form-control">
                        <option value="">Todos</option>
                        <option value="1" {{ request('activo') == '1' ? 'selected' : '' }}>Activos</option>
                        <option value="0" {{ request('activo') == '0' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="es_actual" class="form-control">
                        <option value="">Todos</option>
                        <option value="1" {{ request('es_actual') == '1' ? 'selected' : '' }}>Actual</option>
                        <option value="0" {{ request('es_actual') == '0' ? 'selected' : '' }}>No actual</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    <a href="{{ route('ciclos-escolares.index') }}" class="btn btn-secondary">Limpiar</a>
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
                        <th>Fecha inicio</th>
                        <th>Fecha fin</th>
                        <th>Actual</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ciclos as $ciclo)
                    <tr>
                        <td>{{ $ciclo->id }}</td>
                        <td>{{ $ciclo->nombre }}</td>
                        <td>{{ $ciclo->clave ?? '—' }}</td>
                        <td>{{ $ciclo->fecha_inicio ? \Carbon\Carbon::parse($ciclo->fecha_inicio)->format('d/m/Y') : '—' }}</td>
                        <td>{{ $ciclo->fecha_fin ? \Carbon\Carbon::parse($ciclo->fecha_fin)->format('d/m/Y') : '—' }}</td>
                        <td>
                            @if($ciclo->es_actual)
                                <span class="badge bg-success">Actual</span>
                            @else
                                <span class="badge bg-secondary">—</span>
                            @endif
                        </td>
                        <td>
                            <x-ui.badge :type="$ciclo->activo ? 'success' : 'danger'">
                                {{ $ciclo->activo ? 'Activo' : 'Inactivo' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('ciclos-escolares.show', $ciclo) }}" class="btn btn-sm btn-outline-info">Ver</a>
                            <a href="{{ route('ciclos-escolares.edit', $ciclo) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            <form action="{{ route('ciclos-escolares.destroy', $ciclo) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este ciclo?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4"><x-ui.empty-state message="No hay ciclos escolares" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>{{ $ciclos->links() }}</x-slot:footer>
    </x-ui.card>
</div>
@endsection