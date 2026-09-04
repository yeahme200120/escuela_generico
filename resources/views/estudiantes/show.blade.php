@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-4">{{ $estudiante->nombre_completo }}</h1>

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Información Personal</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Matrícula:</strong> <span class="badge bg-info">{{ $estudiante->matricula ?? 'N/A' }}</span></p>
                            <p class="mb-2"><strong>CURP:</strong> {{ $estudiante->curp ?? '-' }}</p>
                            <p class="mb-2"><strong>Fecha de Nacimiento:</strong> {{ $estudiante->fecha_nacimiento?->format('d/m/Y') ?? '-' }}</p>
                            <p class="mb-2"><strong>Sexo:</strong>
                                @switch($estudiante->sexo)
                                    @case('M')
                                        Masculino
                                        @break
                                    @case('F')
                                        Femenino
                                        @break
                                    @default
                                        {{ $estudiante->sexo ?? '-' }}
                                @endswitch
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Email:</strong> {{ $estudiante->email ?? '-' }}</p>
                            <p class="mb-2"><strong>Teléfono:</strong> {{ $estudiante->telefono ?? '-' }}</p>
                            <p class="mb-2"><strong>Fecha de Ingreso:</strong> {{ $estudiante->fecha_ingreso?->format('d/m/Y') ?? '-' }}</p>
                            <p class="mb-2"><strong>Dirección:</strong> {{ $estudiante->direccion ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Estatus Académico</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Estatus:</strong>
                                <span class="badge bg-{{ $estudiante->estatus == 'activo' ? 'success' : 'warning' }}">
                                    {{ ucfirst(str_replace('_', ' ', $estudiante->estatus)) }}
                                </span>
                            </p>
                            <p class="mb-2">
                                <strong>Situación Académica:</strong>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $estudiante->situacion_academica ?? 'N/A')) }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong>Inscripción:</strong>
                                <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $estudiante->situacion_inscripcion ?? 'N/A')) }}</span>
                            </p>
                            <p class="mb-2">
                                <strong>Estatus de Riesgo:</strong>
                                <span class="badge bg-{{ $estudiante->estatus_riesgo == 'normal' ? 'success' : 'danger' }}">
                                    {{ ucfirst(str_replace('_', ' ', $estudiante->estatus_riesgo)) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($estudiante->organizacion || $estudiante->sede)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Información Institucional</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Organización:</strong> {{ $estudiante->organizacion?->nombre ?? '-' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Sede Actual:</strong> {{ $estudiante->sede?->nombre ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="d-flex gap-2">
                <a href="{{ route('estudiantes.edit', $estudiante) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <form action="{{ route('estudiantes.destroy', $estudiante) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este estudiante?')">
                        <i class="bi bi-trash"></i> Eliminar
                    </button>
                </form>
                <a href="{{ route('estudiantes.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Información del Registro</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Creado:</strong><br>
                        {{ $estudiante->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="mb-2">
                        <strong>Última actualización:</strong><br>
                        {{ $estudiante->updated_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="mb-2">
                        <strong>UUID:</strong><br>
                        <code style="font-size: 0.75rem;">{{ $estudiante->uuid }}</code>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
