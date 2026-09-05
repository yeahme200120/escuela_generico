@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Nueva Escuela" 
        subtitle="Registrar una institución"
        :actions="[
            ['label' => 'Volver', 'route' => route('escuelas.index'), 'icon' => 'arrow-left']
        ]"
    />

    <x-ui.card>
        <form action="{{ route('escuelas.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="organizacion_id" class="form-label">Organización <span class="text-danger">*</span></label>
                    <select name="organizacion_id" id="organizacion_id" class="form-control @error('organizacion_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($organizaciones as $org)
                            <option value="{{ $org->id }}" {{ old('organizacion_id') == $org->id ? 'selected' : '' }}>{{ $org->nombre }}</option>
                        @endforeach
                    </select>
                    @error('organizacion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" required maxlength="200">
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="clave" class="form-label">Clave</label>
                    <input type="text" name="clave" id="clave" class="form-control @error('clave') is-invalid @enderror" value="{{ old('clave') }}" maxlength="50">
                    @error('clave')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}" maxlength="30">
                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}">
                    @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" id="ciudad" class="form-control @error('ciudad') is-invalid @enderror" value="{{ old('ciudad') }}" maxlength="100">
                    @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" value="{{ old('estado') }}" maxlength="100">
                    @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="pais" class="form-label">País</label>
                    <input type="text" name="pais" id="pais" class="form-control @error('pais') is-invalid @enderror" value="{{ old('pais', 'México') }}" maxlength="100">
                    @error('pais')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="codigo_postal" class="form-label">Código postal</label>
                    <input type="text" name="codigo_postal" id="codigo_postal" class="form-control @error('codigo_postal') is-invalid @enderror" value="{{ old('codigo_postal') }}" maxlength="10">
                    @error('codigo_postal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="activa" id="activa" class="form-check-input" value="1" {{ old('activa', true) ? 'checked' : '' }}>
                        <label for="activa" class="form-check-label">Activa</label>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">Guardar Escuela</button>
                    <a href="{{ route('escuelas.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </x-ui.card>
</div>
@endsection