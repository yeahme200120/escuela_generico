@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Alumnos" 
        subtitle="Gestión de estudiantes"
        :actions="[
            ['label' => 'Nuevo Alumno', 'route' => route('alumnos.create'), 'icon' => 'plus'],
            ['label' => 'Inscripción', 'route' => route('alumnos.inscripcion.create'), 'icon' => 'plus', 'variant' => 'success']
        ]"
    />

    <x-ui.card>
        <x-slot:header>
            <form method="GET" action="{{ route('alumnos.index') }}" class="row g-3 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, matrícula o CURP..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="estatus" class="form-control">
                        <option value="">Todos los estatus</option>
                        <option value="activo" {{ request('estatus') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="baja_temporal" {{ request('estatus') == 'baja_temporal' ? 'selected' : '' }}>Baja Temporal</option>
                        <option value="baja_definitiva" {{ request('estatus') == 'baja_definitiva' ? 'selected' : '' }}>Baja Definitiva</option>
                        <option value="egresado" {{ request('estatus') == 'egresado' ? 'selected' : '' }}>Egresado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="situacion_academica" class="form-control">
                        <option value="">Situación académica</option>
                        <option value="regular" {{ request('situacion_academica') == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="irregular" {{ request('situacion_academica') == 'irregular' ? 'selected' : '' }}>Irregular</option>
                        <option value="reprobado" {{ request('situacion_academica') == 'reprobado' ? 'selected' : '' }}>Reprobado</option>
                        <option value="en_regularizacion" {{ request('situacion_academica') == 'en_regularizacion' ? 'selected' : '' }}>En Regularización</option>
                        <option value="condicionado" {{ request('situacion_academica') == 'condicionado' ? 'selected' : '' }}>Condicionado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sede_actual_id" class="form-control">
                        <option value="">Todas las sedes</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ request('sede_actual_id') == $sede->id ? 'selected' : '' }}>{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary" type="submit">Filtrar</button>
                    <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Limpiar</a>
                </div>
            </form>
        </x-slot:header>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Matrícula</th>
                        <th>Nombre completo</th>
                        <th>CURP</th>
                        <th>Sede</th>
                        <th>Estatus</th>
                        <th>Situación</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alumnos as $alumno)
                    <tr>
                        <td>{{ $alumno->id }}</td>
                        <td><strong>{{ $alumno->matricula }}</strong></td>
                        <td>{{ $alumno->nombre_completo }}</td>
                        <td>{{ $alumno->curp ?? '—' }}</td>
                        <td>{{ $alumno->sedeActual->nombre ?? '—' }}</td>
                        <td>
                            <x-ui.badge :type="$alumno->estatus === 'activo' ? 'success' : ($alumno->estatus === 'baja_temporal' ? 'warning' : 'danger')">
                                {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                            </x-ui.badge>
                        </td>
                        <td>
                            <x-ui.badge :type="$alumno->situacion_academica === 'regular' ? 'success' : ($alumno->situacion_academica === 'irregular' ? 'warning' : 'danger')">
                                {{ ucfirst(str_replace('_', ' ', $alumno->situacion_academica)) }}
                            </x-ui.badge>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-sm btn-outline-info">Ver</a>
                            <a href="{{ route('alumnos.edit', $alumno) }}" class="btn btn-sm btn-outline-warning">Editar</a>
                            <form action="{{ route('alumnos.destroy', $alumno) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este alumno? Se eliminará su registro.')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-4"><x-ui.empty-state message="No hay alumnos registrados" /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-slot:footer>{{ $alumnos->links() }}</x-slot:footer>
    </x-ui.card>
</div>
@endsection