@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1 class="mb-4">Editar Estudiante</h1>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('estudiantes.update', $estudiante) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control @error('nombres') is-invalid @enderror"
                                    id="nombres" name="nombres" value="{{ old('nombres', $estudiante->nombres) }}" required>
                                @error('nombres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="apellido_paterno" class="form-label">Apellido Paterno *</label>
                                <input type="text" class="form-control @error('apellido_paterno') is-invalid @enderror"
                                    id="apellido_paterno" name="apellido_paterno" value="{{ old('apellido_paterno', $estudiante->apellido_paterno) }}" required>
                                @error('apellido_paterno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="apellido_materno" class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control @error('apellido_materno') is-invalid @enderror"
                                    id="apellido_materno" name="apellido_materno" value="{{ old('apellido_materno', $estudiante->apellido_materno) }}">
                                @error('apellido_materno')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                    id="fecha_nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $estudiante->fecha_nacimiento?->format('Y-m-d')) }}">
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sexo" class="form-label">Sexo</label>
                                <select class="form-control @error('sexo') is-invalid @enderror" id="sexo" name="sexo">
                                    <option value="">-- Selecciona --</option>
                                    <option value="M" {{ old('sexo', $estudiante->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ old('sexo', $estudiante->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                                    <option value="otro" {{ old('sexo', $estudiante->sexo) == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('sexo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="matricula" class="form-label">Matrícula</label>
                                <input type="text" class="form-control @error('matricula') is-invalid @enderror"
                                    id="matricula" name="matricula" value="{{ old('matricula', $estudiante->matricula) }}">
                                @error('matricula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="curp" class="form-label">CURP</label>
                                <input type="text" class="form-control @error('curp') is-invalid @enderror"
                                    id="curp" name="curp" value="{{ old('curp', $estudiante->curp) }}" maxlength="18">
                                <small class="text-muted">Debe tener 18 caracteres</small>
                                @error('curp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email', $estudiante->email) }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                    id="telefono" name="telefono" value="{{ old('telefono', $estudiante->telefono) }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="organizacion_id" class="form-label">Organización</label>
                                <select class="form-control @error('organizacion_id') is-invalid @enderror" id="organizacion_id" name="organizacion_id">
                                    <option value="">-- Selecciona --</option>
                                    @foreach ($organizaciones as $org)
                                        <option value="{{ $org->id }}" {{ old('organizacion_id', $estudiante->organizacion_id) == $org->id ? 'selected' : '' }}>
                                            {{ $org->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('organizacion_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="sede_actual_id" class="form-label">Sede Actual</label>
                                <select class="form-control @error('sede_actual_id') is-invalid @enderror" id="sede_actual_id" name="sede_actual_id">
                                    <option value="">-- Selecciona --</option>
                                    @foreach ($sedes as $sede)
                                        <option value="{{ $sede->id }}" {{ old('sede_actual_id', $estudiante->sede_actual_id) == $sede->id ? 'selected' : '' }}>
                                            {{ $sede->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sede_actual_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="estatus" class="form-label">Estatus *</label>
                                <select class="form-control @error('estatus') is-invalid @enderror" id="estatus" name="estatus" required>
                                    <option value="">-- Selecciona --</option>
                                    <option value="activo" {{ old('estatus', $estudiante->estatus) == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="baja_temporal" {{ old('estatus', $estudiante->estatus) == 'baja_temporal' ? 'selected' : '' }}>Baja Temporal</option>
                                    <option value="baja_definitiva" {{ old('estatus', $estudiante->estatus) == 'baja_definitiva' ? 'selected' : '' }}>Baja Definitiva</option>
                                    <option value="egresado" {{ old('estatus', $estudiante->estatus) == 'egresado' ? 'selected' : '' }}>Egresado</option>
                                </select>
                                @error('estatus')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control @error('direccion') is-invalid @enderror"
                                id="direccion" name="direccion" rows="3">{{ old('direccion', $estudiante->direccion) }}</textarea>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Actualizar
                            </button>
                            <a href="{{ route('estudiantes.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
