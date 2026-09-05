@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Editar Ciclo Escolar: {{ $ciclo->nombre }}" subtitle="Modificar periodo académico" :actions="[['label' => 'Volver', 'route' => route('ciclos.index')]]" />
    <x-ui.card>
        <form action="{{ route('ciclos.update', $ciclo) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="organizacion_id">Organización <span class="text-danger">*</span></label>
                    <select name="organizacion_id" class="form-control @error('organizacion_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($organizaciones as $org)
                            <option value="{{ $org->id }}" {{ old('organizacion_id', $ciclo->organizacion_id) == $org->id ? 'selected' : '' }}>{{ $org->nombre }}</option>
                        @endforeach
                    </select>
                    @error('organizacion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $ciclo->nombre) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="clave">Clave</label>
                    <input type="text" name="clave" class="form-control @error('clave') is-invalid @enderror" value="{{ old('clave', $ciclo->clave) }}">
                    @error('clave')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="fecha_inicio">Fecha de inicio <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_inicio" class="form-control @error('fecha_inicio') is-invalid @enderror" value="{{ old('fecha_inicio', $ciclo->fecha_inicio ? $ciclo->fecha_inicio->format('Y-m-d') : '') }}" required>
                    @error('fecha_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="fecha_fin">Fecha de fin <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_fin" class="form-control @error('fecha_fin') is-invalid @enderror" value="{{ old('fecha_fin', $ciclo->fecha_fin ? $ciclo->fecha_fin->format('Y-m-d') : '') }}" required>
                    @error('fecha_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" name="es_actual" value="1" {{ old('es_actual', $ciclo->es_actual) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label">Ciclo actual</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', $ciclo->activo) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label">Activo</label>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('ciclos.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection