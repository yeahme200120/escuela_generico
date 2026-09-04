@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>Estudiantes</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('estudiantes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Estudiante
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-light">
            <form action="{{ route('estudiantes.index') }}" method="GET" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nombre o matrícula..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="estatus" class="form-control">
                        <option value="">-- Todos los estatus --</option>
                        <option value="activo" {{ request('estatus') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="baja_temporal" {{ request('estatus') == 'baja_temporal' ? 'selected' : '' }}>Baja Temporal</option>
                        <option value="egresado" {{ request('estatus') == 'egresado' ? 'selected' : '' }}>Egresado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">Buscar</button>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Matrícula</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Estatus</th>
                        <th>Riesgo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($estudiantes as $estudiante)
                        <tr>
                            <td>
                                <span class="badge bg-info">{{ $estudiante->matricula ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $estudiante->nombre_completo }}</td>
                            <td>{{ $estudiante->email ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $estudiante->estatus == 'activo' ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $estudiante->estatus)) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $estudiante->estatus_riesgo == 'normal' ? 'success' : 'danger' }}">
                                    {{ ucfirst(str_replace('_', ' ', $estudiante->estatus_riesgo)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('estudiantes.show', $estudiante) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('estudiantes.edit', $estudiante) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('estudiantes.destroy', $estudiante) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No se encontraron estudiantes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $estudiantes->links() }}
    </div>
</div>
@endsection
