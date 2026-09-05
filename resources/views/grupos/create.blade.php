@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Nuevo Grupo" subtitle="Registrar un grupo" :actions="[['label' => 'Volver', 'route' => route('grupos.index')]]" />
    <x-ui.card>
        <form action="{{ route('grupos.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="sede_id">Sede <span class="text-danger">*</span></label>
                    <select name="sede_id" class="form-control @error('sede_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id') == $sede->id ? 'selected' : '' }}>{{ $sede->nombre }}</option>
                        @endforeach
                    </select>
                    @error('sede_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="ciclo_escolar_id">Ciclo escolar <span class="text-danger">*</span></label>
                    <select name="ciclo_escolar_id" class="form-control @error('ciclo_escolar_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($ciclos as $ciclo)
                            <option value="{{ $ciclo->id }}" {{ old('ciclo_escolar_id') == $ciclo->id ? 'selected' : '' }}>{{ $ciclo->nombre }}</option>
                        @endforeach
                    </select>
                    @error('ciclo_escolar_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="grado_id">Grado <span class="text-danger">*</span></label>
                    <select name="grado_id" class="form-control @error('grado_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($grados as $grado)
                            <option value="{{ $grado->id }}" {{ old('grado_id') == $grado->id ? 'selected' : '' }}>{{ $grado->nombre }}</option>
                        @endforeach
                    </select>
                    @error('grado_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="turno">Turno</label>
                    <input type="text" name="turno" class="form-control @error('turno') is-invalid @enderror" value="{{ old('turno') }}">
                    @error('turno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="capacidad">Capacidad</label>
                    <input type="number" name="capacidad" class="form-control @error('capacidad') is-invalid @enderror" value="{{ old('capacidad') }}" min="0">
                    @error('capacidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="aula_principal_id">Aula principal</label>
                    <select name="aula_principal_id" class="form-control @error('aula_principal_id') is-invalid @enderror">
                        <option value="">Sin aula</option>
                        @foreach($aulas as $aula)
                            <option value="{{ $aula->id }}" {{ old('aula_principal_id') == $aula->id ? 'selected' : '' }}>{{ $aula->nombre }}</option>
                        @endforeach
                    </select>
                    @error('aula_principal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="docente_tutor_id">Docente tutor</label>
                    <select name="docente_tutor_id" class="form-control @error('docente_tutor_id') is-invalid @enderror">
                        <option value="">Sin tutor</option>
                        @foreach($docentes as $docente)
                            <option value="{{ $docente->id }}" {{ old('docente_tutor_id') == $docente->id ? 'selected' : '' }}>{{ $docente->nombre }}</option>
                        @endforeach
                    </select>
                    @error('docente_tutor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label">Activo</label>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('grupos.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection