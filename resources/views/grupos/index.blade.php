@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Grupos" 
        subtitle="Gestión de grupos por sede, ciclo y grado"
        :actions="[
            ['label' => 'Nuevo Grupo', 'route' => route('grupos.create'), 'icon' => 'plus']
        ]"
    />

    <x-ui.card>
        <x-slot:header>
            <form method="GET" action="{{ route('grupos.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="sede_id" class="form-control">
                        <option value="">Todas las sedes</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ request('sede_id') == $sede->id ? 'selected' : '' }}>{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="ciclo_escolar_id" class="form-control">
                        <option value="">Todos los ciclos</option>
                        @foreach($ciclos as $ciclo)
                            <option value="{{ $ciclo->id }}" {{ request('ciclo_escolar_id') == $ciclo->id ? 'selected' : '' }}>{{ $ciclo->nombre }}</option>
                        @endforeach
                    </select>
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
                    <a href="{{ route('grupos.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </x-slot:header>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Sede</th>
                        <th>Ciclo</th>
                        <th>Grado</th>
                        <th>Turno</th>
                        <th>Capacidad</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grupos as $grupo)
                    <tr>
                        <td>{{ $grupo->id }}</td>
                        <td>{{ $grupo->nombre }}</td>
                        <td>{{ $grupo->sede->nombre ?? 'N/A' }}</td>
                        <td>{{ $grupo->cicloEscolar->nombre ?? 'N/A' }}</td>
                        <td>{{ $grupo->grado->nombre ?? 'N/A' }}</td>
                        <td>{{ $grupo->turno ?? '—' }}</td>
                        <td>{{ $grupo->capacidad ?? '—' }}</td>
                        <td>
                            <x-ui.badge :type="$grupo->activo ? 'success' : 'danger'">
                                {{ $grupo->activo ? 'Activo' : 'Inactivo' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('grupos.show', $grupo) }}" class="btn btn-sm btn-outline-info">Ver</a>
                            <a href="{{ route('grupos.edit', $grupo) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            <form action="{{ route('grupos.destroy', $grupo) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este grupo?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center py-4"><x-ui.empty-state message="No hay grupos registrados" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>{{ $grupos->links() }}</x-slot:footer>
    </x-ui.card>
</div>
@endsection