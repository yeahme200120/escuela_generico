@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Editar Alumno: {{ $alumno->nombre_completo }}" 
        subtitle="Modificar datos del estudiante"
        :actions="[
            ['label' => 'Volver', 'route' => route('alumnos.index'), 'icon' => 'arrow-left']
        ]"
    />

    <x-ui.card>
        <form action="{{ route('alumnos.update', $alumno) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">

                {{-- Datos de identificación --}}
                <div class="col-12">
                    <h6 class="border-bottom pb-2">Datos personales</h6>
                </div>

                <div class="col-md-6">
                    <label for="organizacion_id">Organización <span class="text-danger">*</span></label>
                    <select name="organizacion_id" class="form-control @error('organizacion_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($organizaciones as $org)
                            <option value="{{ $org->id }}" {{ old('organizacion_id', $alumno->organizacion_id) == $org->id ? 'selected' : '' }}>{{ $org->nombre }}</option>
                        @endforeach
                    </select>
                    @error('organizacion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="sede_actual_id">Sede actual</label>
                    <select name="sede_actual_id" class="form-control @error('sede_actual_id') is-invalid @enderror">
                        <option value="">Seleccionar...</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_actual_id', $alumno->sede_actual_id) == $sede->id ? 'selected' : '' }}>{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                    @error('sede_actual_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="matricula">Matrícula <span class="text-danger">*</span></label>
                    <input type="text" name="matricula" class="form-control @error('matricula') is-invalid @enderror" value="{{ old('matricula', $alumno->matricula) }}" required>
                    @error('matricula')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="curp">CURP</label>
                    <input type="text" name="curp" class="form-control @error('curp') is-invalid @enderror" value="{{ old('curp', $alumno->curp) }}" maxlength="18">
                    @error('curp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2">
                    <label for="sexo">Sexo</label>
                    <select name="sexo" class="form-control @error('sexo') is-invalid @enderror">
                        <option value="">Seleccionar</option>
                        <option value="Masculino" {{ old('sexo', $alumno->sexo) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Femenino" {{ old('sexo', $alumno->sexo) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                        <option value="Otro" {{ old('sexo', $alumno->sexo) == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('sexo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="fecha_nacimiento">Fecha de nacimiento <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror" value="{{ old('fecha_nacimiento', $alumno->fecha_nacimiento ? $alumno->fecha_nacimiento->format('Y-m-d') : '') }}" required>
                    @error('fecha_nacimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="nombres">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror" value="{{ old('nombres', $alumno->nombres) }}" required>
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="apellido_paterno">Apellido paterno <span class="text-danger">*</span></label>
                    <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror" value="{{ old('apellido_paterno', $alumno->apellido_paterno) }}" required>
                    @error('apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="apellido_materno">Apellido materno</label>
                    <input type="text" name="apellido_materno" class="form-control @error('apellido_materno') is-invalid @enderror" value="{{ old('apellido_materno', $alumno->apellido_materno) }}">
                    @error('apellido_materno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Contacto --}}
                <div class="col-12 mt-3">
                    <h6 class="border-bottom pb-2">Contacto</h6>
                </div>

                <div class="col-md-4">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $alumno->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="telefono">Teléfono</label>
                    <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $alumno->telefono) }}">
                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="celular">Celular</label>
                    <input type="text" name="celular" class="form-control @error('celular') is-invalid @enderror" value="{{ old('celular', $alumno->celular) }}">
                    @error('celular')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Dirección --}}
                <div class="col-12 mt-3">
                    <h6 class="border-bottom pb-2">Dirección</h6>
                </div>

                <div class="col-12">
                    <label for="direccion">Dirección</label>
                    <input type="text" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $alumno->direccion) }}">
                    @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="ciudad">Ciudad</label>
                    <input type="text" name="ciudad" class="form-control @error('ciudad') is-invalid @enderror" value="{{ old('ciudad', $alumno->ciudad) }}">
                    @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="estado">Estado</label>
                    <input type="text" name="estado" class="form-control @error('estado') is-invalid @enderror" value="{{ old('estado', $alumno->estado) }}">
                    @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="pais">País</label>
                    <input type="text" name="pais" class="form-control @error('pais') is-invalid @enderror" value="{{ old('pais', $alumno->pais ?? 'México') }}">
                    @error('pais')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="codigo_postal">Código postal</label>
                    <input type="text" name="codigo_postal" class="form-control @error('codigo_postal') is-invalid @enderror" value="{{ old('codigo_postal', $alumno->codigo_postal) }}">
                    @error('codigo_postal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Datos académicos --}}
                <div class="col-12 mt-3">
                    <h6 class="border-bottom pb-2">Datos académicos</h6>
                </div>

                <div class="col-md-4">
                    <label for="fecha_ingreso">Fecha de ingreso</label>
                    <input type="date" name="fecha_ingreso" class="form-control @error('fecha_ingreso') is-invalid @enderror" value="{{ old('fecha_ingreso', $alumno->fecha_ingreso ? $alumno->fecha_ingreso->format('Y-m-d') : '') }}">
                    @error('fecha_ingreso')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="estatus">Estatus</label>
                    <select name="estatus" class="form-control @error('estatus') is-invalid @enderror">
                        <option value="activo" {{ old('estatus', $alumno->estatus) == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="baja_temporal" {{ old('estatus', $alumno->estatus) == 'baja_temporal' ? 'selected' : '' }}>Baja Temporal</option>
                        <option value="baja_definitiva" {{ old('estatus', $alumno->estatus) == 'baja_definitiva' ? 'selected' : '' }}>Baja Definitiva</option>
                        <option value="egresado" {{ old('estatus', $alumno->estatus) == 'egresado' ? 'selected' : '' }}>Egresado</option>
                    </select>
                    @error('estatus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="situacion_academica">Situación académica</label>
                    <select name="situacion_academica" class="form-control @error('situacion_academica') is-invalid @enderror">
                        <option value="regular" {{ old('situacion_academica', $alumno->situacion_academica) == 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="irregular" {{ old('situacion_academica', $alumno->situacion_academica) == 'irregular' ? 'selected' : '' }}>Irregular</option>
                        <option value="reprobado" {{ old('situacion_academica', $alumno->situacion_academica) == 'reprobado' ? 'selected' : '' }}>Reprobado</option>
                        <option value="en_regularizacion" {{ old('situacion_academica', $alumno->situacion_academica) == 'en_regularizacion' ? 'selected' : '' }}>En Regularización</option>
                        <option value="condicionado" {{ old('situacion_academica', $alumno->situacion_academica) == 'condicionado' ? 'selected' : '' }}>Condicionado</option>
                    </select>
                    @error('situacion_academica')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', $alumno->activo) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label">Activo</label>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Actualizar Alumno</button>
                    <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection