@extends('components.layouts.app')

@section('title', 'Inscribir Alumno')

@push('styles')
<style>
    .inscripcion-summary {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .inscripcion-summary dt {
        font-weight: 600;
        color: #495057;
    }
    .inscripcion-summary dd {
        margin-bottom: 8px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Inscribir Alumno" 
        subtitle="Completar datos de inscripción"
        :actions="[
            ['label' => 'Volver', 'route' => route('alumnos.inscripcion.index'), 'icon' => 'arrow-left']
        ]"
    />

    {{-- Resumen del alumno --}}
    <div class="inscripcion-summary">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1">{{ $alumno->nombre_completo }}</h5>
                <p class="mb-0 text-muted">
                    <strong>Matrícula:</strong> {{ $alumno->matricula }} &nbsp;|&nbsp;
                    <strong>CURP:</strong> {{ $alumno->curp ?? 'No registrado' }} &nbsp;|&nbsp;
                    <strong>Fecha de nacimiento:</strong> {{ $alumno->fecha_nacimiento?->format('d/m/Y') ?? '—' }}
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <x-ui.badge :type="$alumno->estatus === 'activo' ? 'success' : 'danger'">
                    {{ ucfirst(str_replace('_', ' ', $alumno->estatus)) }}
                </x-ui.badge>
                <x-ui.badge :type="$alumno->situacion_academica === 'regular' ? 'success' : 'warning'">
                    {{ ucfirst(str_replace('_', ' ', $alumno->situacion_academica)) }}
                </x-ui.badge>
            </div>
        </div>
    </div>

    <x-ui.card>
        <form id="inscripcionForm" action="{{ route('alumnos.inscripcion.store') }}" method="POST">
            @csrf
            <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">

            <div class="row g-3">

                {{-- Datos de inscripción --}}
                <div class="col-12">
                    <h6 class="border-bottom pb-2">Datos de inscripción</h6>
                </div>

                <div class="col-md-6">
                    <label for="sede_id">Sede <span class="text-danger">*</span></label>
                    <select name="sede_id" id="sede_id" class="form-control @error('sede_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id', $alumno->sede_actual_id) == $sede->id ? 'selected' : '' }}>
                                {{ $sede->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('sede_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="ciclo_escolar_id">Ciclo escolar <span class="text-danger">*</span></label>
                    <select name="ciclo_escolar_id" id="ciclo_escolar_id" class="form-control @error('ciclo_escolar_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($ciclos as $ciclo)
                            <option value="{{ $ciclo->id }}" 
                                    {{ old('ciclo_escolar_id', $cicloActual?->id) == $ciclo->id ? 'selected' : '' }}>
                                {{ $ciclo->nombre }} 
                                @if($ciclo->es_actual)
                                    <span class="badge bg-success">Actual</span>
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('ciclo_escolar_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="nivel_educativo_id">Nivel educativo <span class="text-danger">*</span></label>
                    <select name="nivel_educativo_id" id="nivel_educativo_id" class="form-control @error('nivel_educativo_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($niveles as $nivel)
                            <option value="{{ $nivel->id }}" {{ old('nivel_educativo_id') == $nivel->id ? 'selected' : '' }}>
                                {{ $nivel->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('nivel_educativo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="grado_id">Grado <span class="text-danger">*</span></label>
                    <select name="grado_id" id="grado_id" class="form-control @error('grado_id') is-invalid @enderror" required disabled>
                        <option value="">Primero selecciona un nivel</option>
                    </select>
                    @error('grado_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="grupo_id">Grupo <span class="text-danger">*</span></label>
                    <select name="grupo_id" id="grupo_id" class="form-control @error('grupo_id') is-invalid @enderror" required disabled>
                        <option value="">Primero selecciona grado y sede</option>
                    </select>
                    @error('grupo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted" id="grupo_info"></small>
                </div>

                <div class="col-md-6">
                    <label for="fecha_inscripcion">Fecha de inscripción</label>
                    <input type="date" name="fecha_inscripcion" class="form-control @error('fecha_inscripcion') is-invalid @enderror" 
                           value="{{ old('fecha_inscripcion', now()->toDateString()) }}">
                    @error('fecha_inscripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="observaciones">Observaciones</label>
                    <textarea name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="2">{{ old('observaciones') }}</textarea>
                    @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Opciones adicionales --}}
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="generar_cargos" id="generar_cargos" class="form-check-input" value="1" {{ old('generar_cargos') ? 'checked' : '' }}>
                        <label for="generar_cargos" class="form-check-label">
                            Generar cargos por inscripción (si está configurado)
                        </label>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-success btn-lg" id="btnInscribir">
                        <i class="bi bi-check-circle"></i> Inscribir alumno
                    </button>
                    <a href="{{ route('alumnos.inscripcion.index') }}" class="btn btn-secondary btn-lg">Cancelar</a>
                </div>

            </div>
        </form>
    </x-ui.card>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const sedeSelect = document.getElementById('sede_id');
    const cicloSelect = document.getElementById('ciclo_escolar_id');
    const nivelSelect = document.getElementById('nivel_educativo_id');
    const gradoSelect = document.getElementById('grado_id');
    const grupoSelect = document.getElementById('grupo_id');
    const grupoInfo = document.getElementById('grupo_info');
    const btnInscribir = document.getElementById('btnInscribir');

    // Cargar grados al seleccionar nivel
    nivelSelect.addEventListener('change', function() {
        const nivelId = this.value;
        gradoSelect.disabled = true;
        gradoSelect.innerHTML = '<option value="">Cargando...</option>';

        if (!nivelId) {
            gradoSelect.innerHTML = '<option value="">Primero selecciona un nivel</option>';
            grupoSelect.disabled = true;
            grupoSelect.innerHTML = '<option value="">Primero selecciona grado</option>';
            return;
        }

        fetch(`{{ route('alumnos.inscripcion.grados') }}?nivel_id=${nivelId}`)
            .then(response => response.json())
            .then(data => {
                gradoSelect.disabled = false;
                gradoSelect.innerHTML = '<option value="">Seleccionar grado...</option>';
                data.forEach(grado => {
                    gradoSelect.innerHTML += `<option value="${grado.id}">${grado.nombre} ${grado.clave ? '('+grado.clave+')' : ''}</option>`;
                });
            })
            .catch(() => {
                gradoSelect.innerHTML = '<option value="">Error al cargar grados</option>';
            });
    });

    // Cargar grupos al seleccionar grado, sede y ciclo
    function cargarGrupos() {
        const gradoId = gradoSelect.value;
        const sedeId = sedeSelect.value;
        const cicloId = cicloSelect.value;

        grupoSelect.disabled = true;
        grupoSelect.innerHTML = '<option value="">Cargando...</option>';
        grupoInfo.textContent = '';

        if (!gradoId || !sedeId || !cicloId) {
            grupoSelect.innerHTML = '<option value="">Selecciona grado, sede y ciclo</option>';
            return;
        }

        fetch(`{{ route('alumnos.inscripcion.grupos') }}?grado_id=${gradoId}&sede_id=${sedeId}&ciclo_id=${cicloId}`)
            .then(response => response.json())
            .then(data => {
                grupoSelect.disabled = false;
                grupoSelect.innerHTML = '<option value="">Seleccionar grupo...</option>';
                
                if (data.length === 0) {
                    grupoSelect.innerHTML = '<option value="">No hay grupos disponibles</option>';
                    grupoInfo.textContent = 'No hay grupos disponibles con los criterios seleccionados.';
                    return;
                }

                data.forEach(grupo => {
                    const disponible = grupo.cupos_disponibles > 0;
                    grupoSelect.innerHTML += `
                        <option value="${grupo.id}" ${!disponible ? 'disabled' : ''}>
                            ${grupo.nombre} ${grupo.turno ? '('+grupo.turno+')' : ''} 
                            - ${grupo.cupos_disponibles} cupos disponibles
                        </option>
                    `;
                });

                // Mostrar información del primer grupo
                if (data.length > 0) {
                    const primerGrupo = data[0];
                    grupoInfo.textContent = `Capacidad: ${primerGrupo.capacidad} alumnos | Cupos disponibles: ${primerGrupo.cupos_disponibles}`;
                }
            })
            .catch(() => {
                grupoSelect.innerHTML = '<option value="">Error al cargar grupos</option>';
            });
    }

    gradoSelect.addEventListener('change', cargarGrupos);
    sedeSelect.addEventListener('change', cargarGrupos);
    cicloSelect.addEventListener('change', cargarGrupos);

    // Mostrar información del grupo seleccionado
    grupoSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected && selected.value) {
            const texto = selected.text;
            grupoInfo.textContent = texto;
        }
    });

    // Confirmación antes de inscribir
    document.getElementById('inscripcionForm').addEventListener('submit', function(e) {
        const alumnoNombre = '{{ $alumno->nombre_completo }}';
        const grupoNombre = grupoSelect.options[grupoSelect.selectedIndex]?.text || '';

        if (!confirm(`¿Confirmas inscribir a ${alumnoNombre} en el grupo seleccionado?`)) {
            e.preventDefault();
        }
    });
});
</script>
@endpush