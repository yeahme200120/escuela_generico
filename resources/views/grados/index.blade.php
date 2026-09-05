@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Grados" 
        subtitle="Gestión de grados por nivel educativo"
        :actions="[
            ['label' => 'Nuevo Grado', 'route' => route('grados.create'), 'icon' => 'plus']
        ]"
    />

    <x-ui.card>
        <x-slot:header>
            <form method="GET" action="{{ route('grados.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="nivel_educativo_id" class="form-control">
                        <option value="">Todos los niveles</option>
                        @foreach($niveles as $nivel)
                            <option value="{{ $nivel->id }}" {{ request('nivel_educativo_id') == $nivel->id ? 'selected' : '' }}>{{ $nivel->nombre }}</option>
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
                    <a href="{{ route('grados.index') }}" class="btn btn-secondary">Limpiar</a>
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
                        <th>Nivel</th>
                        <th>Orden</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grados as $grado)
                    <tr>
                        <td>{{ $grado->id }}</td>
                        <td>{{ $grado->nombre }}</td>
                        <td>{{ $grado->clave ?? '—' }}</td>
                        <td>{{ $grado->nivelEducativo->nombre ?? 'N/A' }}</td>
                        <td>{{ $grado->orden ?? '—' }}</td>
                        <td>
                            <x-ui.badge :type="$grado->activo ? 'success' : 'danger'">
                                {{ $grado->activo ? 'Activo' : 'Inactivo' }}
                            </x-ui.badge>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('grados.show', $grado) }}" class="btn btn-sm btn-outline-info">Ver</a>
                            <a href="{{ route('grados.edit', $grado) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            <form action="{{ route('grados.destroy', $grado) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este grado?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-4"><x-ui.empty-state message="No hay grados registrados" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>{{ $grados->links() }}</x-slot:footer>
    </x-ui.card>
</div>
@endsection