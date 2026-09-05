@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Nuevo Nivel Educativo" subtitle="Registrar un nivel" :actions="[['label' => 'Volver', 'route' => route('niveles.index')]]" />
    <x-ui.card>
        <form action="{{ route('niveles.update', $nivel->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="organizacion_id">Organización <span class="text-danger">*</span></label>
                    <select name="organizacion_id" class="form-control @error('organizacion_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($organizaciones as $org)
                            <option value="{{ $org->id }}" {{ old('organizacion_id') == $org->id ? 'selected' : '' }}>{{ $org->nombre }}</option>
                        @endforeach
                    </select>
                    @error('organizacion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="nombre">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="clave">Clave</label>
                    <input type="text" name="clave" class="form-control @error('clave') is-invalid @enderror" value="{{ old('clave') }}">
                    @error('clave')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="orden">Orden</label>
                    <input type="number" name="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden') }}" min="0">
                    @error('orden')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }} class="form-check-input">
                        <label class="form-check-label">Activo</label>
                    </div>
                </div>
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="{{ route('niveles.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection