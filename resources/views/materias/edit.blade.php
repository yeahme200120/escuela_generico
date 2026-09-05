@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Editar Materia: {{ $materia->nombre }}" subtitle="Modificar materia" :actions="[['label' => 'Volver', 'route' => route('materias.index')]]" />
    <x-ui.card>
        <form action="{{ route('materias.update', $materia) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="escuela_id">Escuela <span class="text-danger">*</span></label>
                    <select name="escuela_id" class="form-control @error('escuela_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($escuelas as $escuela)
                            <option value="{{ $escuela->id }}" {{ old('escuela_id', $materia->escuela_id) == $escuela->id ? 'selected' : '' }}>{{ $escuela->nombre }}</option>
                        @endforeach
                    </select>
                    @error('escuela_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="clave">Clave <span class="text-danger">*</span></label>
                    <input type="text" name="clave" class="form-control @error('clave') is-invalid @enderror" value="{{ old('clave', $materia->clave) }}" required>
                    @error('clave')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $materia->nombre) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="tipo">Tipo</label>
                    <select name="tipo" class="form-control @error('tipo') is-invalid @enderror">
                        <option value="">Seleccionar...</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}" {{ old('tipo', $materia->tipo) == $tipo ? 'selected' : '' }}>{{ ucfirst($tipo) }}</option>
                        @endforeach
                    </select>
                    @error('tipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="horas_semana">Horas por semana</label>
                    <input type="number" name="horas_semana" class="form-control @error('horas_semana') is-invalid @enderror" value="{{ old('horas_semana', $materia->horas_semana) }}" min="0">
                    @error('horas_semana')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="creditos">Créditos</label>
                    <input type="number" name="creditos" class="form-control @error('creditos') is-invalid @enderror" value="{{ old('creditos', $materia->creditos) }}" min="0">
                    @error('creditos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="activa" value="1" {{ old('activa', $materia->activa) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label">Activa</label>
                    </div>
                </div>
                <div class="col-12">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="3">{{ old('descripcion', $materia->descripcion) }}</textarea>
                    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('materias.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection