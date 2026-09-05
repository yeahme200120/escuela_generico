@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Editar Grado: {{ $grado->nombre }}" subtitle="Modificar grado" :actions="[['label' => 'Volver', 'route' => route('grados.index')]]" />
    <x-ui.card>
        <form action="{{ route('grados.update', $grado) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="organizacion_id">Organización <span class="text-danger">*</span></label>
                    <select name="organizacion_id" class="form-control @error('organizacion_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($organizaciones as $org)
                            <option value="{{ $org->id }}" {{ old('organizacion_id', $grado->organizacion_id) == $org->id ? 'selected' : '' }}>{{ $org->nombre }}</option>
                        @endforeach
                    </select>
                    @error('organizacion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="nivel_educativo_id">Nivel educativo <span class="text-danger">*</span></label>
                    <select name="nivel_educativo_id" class="form-control @error('nivel_educativo_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($niveles as $nivel)
                            <option value="{{ $nivel->id }}" {{ old('nivel_educativo_id', $grado->nivel_educativo_id) == $nivel->id ? 'selected' : '' }}>{{ $nivel->nombre }}</option>
                        @endforeach
                    </select>
                    @error('nivel_educativo_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $grado->nombre) }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="clave">Clave</label>
                    <input type="text" name="clave" class="form-control @error('clave') is-invalid @enderror" value="{{ old('clave', $grado->clave) }}">
                    @error('clave')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="orden">Orden</label>
                    <input type="number" name="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', $grado->orden) }}" min="0">
                    @error('orden')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', $grado->activo) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label">Activo</label>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('grados.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection