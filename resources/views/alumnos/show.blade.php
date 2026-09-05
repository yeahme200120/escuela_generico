@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Alumno: {{ $alumno->nombre_completo }}" 
        subtitle="Detalles del estudiante"
        :actions="[
            ['label' => 'Editar', 'route' => route('alumnos.edit', $alumno)],
            ['label' => 'Inscribir', 'route' => route('alumnos.inscripcion.create', $alumno), 'variant' => 'success'],
            ['label' => 'Volver', 'route' => route('alumnos.index')]
        ]"
    />

    <div class="row g-3">

        {{-- Datos personales --}}
        <div class="col-md-6">
            <x-ui.card>
                <x-slot:header><h6 class="mb-0 fw-bold">Datos personales</h6></x-slot:header>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Matrícula</dt><dd class="col-sm-8"><strong>{{ $alumno->matricula }}</strong></dd>
                    <dt class="col-sm-4">CURP</dt><dd class="col-sm-8">{{ $alumno->curp ?? '—' }}</dd>
                    <dt class="col-sm-4">Nombre completo</dt><dd class="col-sm-8">{{ $alumno->nombre_completo }}</dd>
                    <dt class="col-sm-4">Fecha de nacimiento</dt><dd class="col-sm-8">{{ $alumno->fecha_nacimiento ? $alumno->fecha_nacimiento->format('d/m/Y') : '—' }}</dd>
                    <dt class="col-sm-4">Sexo</dt><dd class="col-sm-8">{{ $alumno->sexo ?? '—' }}</dd>
                    <dt class="col-sm-4">Organización</dt><dd class="col-sm-8">{{ $alumno->organizacion->nombre ?? 'N/A' }}</dd>
                    <dt class="col-sm-4">Sede actual</dt><dd class="col-sm-8">{{ $alumno->sedeActual->nombre ?? '—' }}</dd>
                </dl>
            </x-ui.card>
        </div>

        {{-- Contacto y dirección --}}
        <div class="col-md-6">
            <x-ui.card>
                <x-slot:header><h6 class="mb-0 fw-bold">Contacto y dirección</h6></x-slot:header>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Email</dt><dd class="col-sm-8">{{ $alumno->email ?? '—' }}</dd>
                    <dt class="col-sm-4">Teléfono</dt><dd class="col-sm-8">{{ $alumno->telefono ?? '—' }}</dd>
                    <dt class="col-sm-4">Celular</dt><dd class="col-sm-8">{{ $alumno->celular ?? '—' }}</dd>
                    <dt class="col-sm-4">Dirección</dt><dd class="col-sm-8">{{ $alumno->direccion ?? '—' }}</dd>
                    <dt class="col-sm-4">Ciudad</dt><dd class="col-sm-8">{{ $alumno->ciudad ?? '—' }}</dd>
                    <dt class="col-sm-4">Estado</dt><dd class="col-sm-8">{{ $alumno->estado ?? '—' }}</dd>
                    <dt class="col-sm-4">País</dt><dd class="col-sm-8">{{ $alumno->pais ?? '—' }}</dd>
                    <dt class="col-sm-4">Código postal</dt><dd class="col-sm-8">{{ $alumno->codigo_postal ?? '—' }}</dd>
                </dl>
            </x-ui.card>
        </div>

        {{-- Datos académicos --}}
        <div class="col-md-6">
            <x-ui.card>
                <x-slot:header><h6 class="mb-0 fw-bold">Datos académicos</h6></x-slot:header>
                <dl class="row mb-0">
                    <dt class="col-sm-4">Fecha de ingreso</dt><dd class="col-sm-8">{{ $alumno->fecha_ingreso ? $alumno->fecha_ingreso->format('d/m/Y') : '—' }}</dd>
                    <dt class="col-sm-4">Estatus</dt><dd class="col-sm-8">
                        <x-ui.badge :type="$alumno->estatus === 'activo' ? 'success' : ($alumno->estatus === 'baja_temporal' ? 'warning' : 'danger')">
                            {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                        </x-ui.badge>
                    </dd>
                    <dt class="col-sm-4">Situación académica</dt><dd class="col-sm-8">
                        <x-ui.badge :type="$alumno->situacion_academica === 'regular' ? 'success' : ($alumno->situacion_academica === 'irregular' ? 'warning' : 'danger')">
                            {{ ucfirst(str_replace('_', ' ', $alumno->situacion_academica)) }}
                        </x-ui.badge>
                    </dd>
                    <dt class="col-sm-4">Activo</dt><dd class="col-sm-8">{{ $alumno->activo ? 'Sí' : 'No' }}</dd>
                </dl>
            </x-ui.card>
        </div>

        {{-- Trayectoria --}}
        <div class="col-md-6">
            <x-ui.card>
                <x-slot:header>
                    <h6 class="mb-0 fw-bold">Trayectoria escolar</h6>
                </x-slot:header>
                @if($alumno->trayectorias->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ciclo</th>
                                    <th>Grado</th>
                                    <th>Grupo</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($alumno->trayectorias->take(5) as $tray)
                                <tr>
                                    <td>{{ $tray->cicloEscolar->nombre ?? '—' }}</td>
                                    <td>{{ $tray->grado->nombre ?? '—' }}</td>
                                    <td>{{ $tray->grupo->nombre ?? '—' }}</td>
                                    <td><x-ui.badge :type="$tray->estatus === 'activo' ? 'success' : 'secondary'">{{ $tray->estatus }}</x-ui.badge></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($alumno->trayectorias->count() > 5)
                        <p class="text-muted small">+ {{ $alumno->trayectorias->count() - 5 }} registros más</p>
                    @endif
                @else
                    <p class="text-muted">Sin trayectoria registrada</p>
                @endif
            </x-ui.card>
        </div>

        {{-- Tutores --}}
        <div class="col-12">
            <x-ui.card>
                <x-slot:header>
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">Tutores</h6>
                        <a href="#" class="btn btn-sm btn-primary">Agregar tutor</a>
                    </div>
                </x-slot:header>
                @if($alumno->tutores->count() > 0)
                    <ul class="list-unstyled mb-0">
                        @foreach($alumno->tutores as $tutor)
                            <li class="border-bottom py-2">{{ $tutor->nombre_completo }} <span class="text-muted small">({{ $tutor->relacion }})</span></li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Sin tutores registrados</p>
                @endif
            </x-ui.card>
        </div>

    </div>
</div>
@endsection