@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Materias" 
        subtitle="Catálogo de materias por escuela"
        :actions="[
            ['label' => 'Nueva Materia', 'route' => route('materias.create'), 'icon' => 'plus']
        ]"
    />

    <x-ui.card>
        <x-slot:header>
            <form method="GET" action="{{ route('materias.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="escuela_id" class="form-control">
                        <option value="">Todas las escuelas</option>
                        @foreach($escuelas as $escuela)
                            <option value="{{ $escuela->id }}" {{ request('escuela_id') == $escuela->id ? 'selected' : '' }}>{{ $escuela->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="tipo" class="form-control">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>{{ ucfirst($tipo) }}</option>
                        @endforeach
                    </select>
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
                    <a href="{{ route('materias.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </x-slot:header>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Clave</th>
                        <th>Nombre</th>
                        <th>Escuela</th>
                        <th>Tipo</th>
                        <th>Horas</th>
                        <th>Créditos</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materias as $materia)
                    <tr>
                        <td>{{ $materia->id }}</td>
                        <td>{{ $materia->clave }}</td>
                        <td>{{ $materia->nombre }}</td>
                        <td>{{ $materia->escuela->nombre ?? 'N/A' }}</td>
                        <td>{{ ucfirst($materia->tipo ?? '—') }}</td>
                        <td>{{ $materia->horas_semana ?? '—' }}</td>
                        <td>{{ $materia->creditos ?? '—' }}</td>
                        <td>
                            <x-ui.badge :type="$materia->activa ? 'success' : 'danger'">
                                {{ $materia->activa ? 'Activa' : 'Inactiva' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('materias.show', $materia) }}" class="btn btn-sm btn-outline-info">Ver</a>
                            <a href="{{ route('materias.edit', $materia) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            <form action="{{ route('materias.destroy', $materia) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta materia?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4"><x-ui.empty-state message="No hay materias registradas" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>{{ $materias->links() }}</x-slot:footer>
    </x-ui.card>
</div>
@endsection