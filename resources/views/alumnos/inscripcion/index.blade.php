@extends('components.layouts.app')

@section('title', 'Inscripción de Alumnos')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Inscripción de Alumnos" 
        subtitle="Buscar alumno para inscribir"
        :actions="[
            ['label' => 'Volver', 'route' => route('alumnos.index'), 'icon' => 'arrow-left']
        ]"
    />

    <x-ui.card>
        <x-slot:header>
            <form method="GET" action="{{ route('alumnos.inscripcion.index') }}" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control form-control-lg" 
                           placeholder="Buscar por nombre, matrícula o CURP..." 
                           value="{{ request('search') }}" autofocus>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-search"></i> Buscar alumno
                    </button>
                </div>
            </form>
        </x-slot:header>

        @if($search)
            @if($alumno)
                <div class="row g-3">
                    <div class="col-12">
                        <div class="alert alert-success d-flex align-items-center gap-3">
                            <i class="bi bi-person-check fs-3"></i>
                            <div>
                                <h5 class="mb-0">Alumno encontrado</h5>
                                <p class="mb-0"><strong>{{ $alumno->nombre_completo }}</strong> — Matrícula: {{ $alumno->matricula }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-grid gap-3 d-sm-flex">
                            <a href="{{ route('alumnos.inscripcion.create', ['alumno_id' => $alumno->id]) }}" 
                               class="btn btn-success btn-lg">
                                <i class="bi bi-pencil-square"></i> Inscribir alumno
                            </a>
                            <a href="{{ route('alumnos.show', $alumno) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-eye"></i> Ver ficha
                            </a>
                            <a href="{{ route('alumnos.inscripcion.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-repeat"></i> Nueva búsqueda
                            </a>
                        </div>
                    </div>

                    {{-- Datos rápidos del alumno --}}
                    <div class="col-12 mt-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted d-block">CURP</small>
                                    <strong>{{ $alumno->curp ?? 'No registrado' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted d-block">Fecha de nacimiento</small>
                                    <strong>{{ $alumno->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted d-block">Sede actual</small>
                                    <strong>{{ $alumno->sedeActual->nombre ?? '—' }}</strong>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-light p-2 rounded">
                                    <small class="text-muted d-block">Estatus</small>
                                    <x-ui.badge :type="$alumno->estatus === 'activo' ? 'success' : 'danger'">
                                        {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                                    </x-ui.badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <x-ui.empty-state 
                        message="No se encontró ningún alumno con los criterios de búsqueda" 
                        icon="person-x"
                    />
                    <p class="text-muted">Verifica que el nombre, matrícula o CURP sea correcto.</p>
                    <a href="{{ route('alumnos.inscripcion.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-repeat"></i> Nueva búsqueda
                    </a>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <x-ui.empty-state 
                    message="Busca un alumno para iniciar la inscripción" 
                    icon="search"
                />
                <p class="text-muted">Ingresa el nombre, matrícula o CURP del alumno.</p>
            </div>
        @endif
    </x-ui.card>
</div>
@endsection