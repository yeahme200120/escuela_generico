@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Editar Sede: {{ $sede->nombre }}" 
        subtitle="Modificar datos del campus"
        :actions="[
            ['label' => 'Volver', 'route' => route('sedes.index'), 'icon' => 'arrow-left']
        ]"
    />

    <x-ui.card>
        <form action="{{ route('sedes.update', $sede) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">

                <!-- ========================================= -->
                <!-- 1. DATOS GENERALES                        -->
                <!-- ========================================= -->

                <div class="col-md-6">
                    <label for="organizacion_id" class="form-label">Organización <span class="text-danger">*</span></label>
                    <select name="organizacion_id" id="organizacion_id" class="form-control @error('organizacion_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($organizaciones as $org)
                            <option value="{{ $org->id }}" {{ old('organizacion_id', $sede->organizacion_id) == $org->id ? 'selected' : '' }}>
                                {{ $org->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('organizacion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="escuela_id" class="form-label">Escuela <span class="text-danger">*</span></label>
                    <select name="escuela_id" id="escuela_id" class="form-control @error('escuela_id') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($escuelas as $esc)
                            <option value="{{ $esc->id }}" {{ old('escuela_id', $sede->escuela_id) == $esc->id ? 'selected' : '' }}>
                                {{ $esc->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('escuela_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="nombre" class="form-label">Nombre de la Sede <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                           value="{{ old('nombre', $sede->nombre) }}" required maxlength="200">
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="clave" class="form-label">Clave (código corto)</label>
                    <input type="text" name="clave" id="clave" class="form-control @error('clave') is-invalid @enderror" 
                           value="{{ old('clave', $sede->clave) }}" maxlength="50">
                    @error('clave')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- ========================================= -->
                <!-- 2. CONTACTO Y DIRECCIÓN                  -->
                <!-- ========================================= -->

                <div class="col-md-6">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email', $sede->email) }}" maxlength="255">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                           value="{{ old('telefono', $sede->telefono) }}" maxlength="30">
                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                           value="{{ old('direccion', $sede->direccion) }}">
                    @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <input type="text" name="ciudad" id="ciudad" class="form-control @error('ciudad') is-invalid @enderror" 
                           value="{{ old('ciudad', $sede->ciudad) }}" maxlength="100">
                    @error('ciudad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="estado" class="form-label">Estado</label>
                    <input type="text" name="estado" id="estado" class="form-control @error('estado') is-invalid @enderror" 
                           value="{{ old('estado', $sede->estado) }}" maxlength="100">
                    @error('estado')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="pais" class="form-label">País</label>
                    <input type="text" name="pais" id="pais" class="form-control @error('pais') is-invalid @enderror" 
                           value="{{ old('pais', $sede->pais ?? 'México') }}" maxlength="100">
                    @error('pais')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="codigo_postal" class="form-label">Código postal</label>
                    <input type="text" name="codigo_postal" id="codigo_postal" class="form-control @error('codigo_postal') is-invalid @enderror" 
                           value="{{ old('codigo_postal', $sede->codigo_postal) }}" maxlength="10">
                    @error('codigo_postal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- ========================================= -->
                <!-- 3. GEOLOCALIZACIÓN                        -->
                <!-- ========================================= -->

                <div class="col-md-4">
                    <label for="latitud" class="form-label">Latitud</label>
                    <input type="number" step="any" name="latitud" id="latitud" class="form-control @error('latitud') is-invalid @enderror" 
                           value="{{ old('latitud', $sede->latitud) }}" placeholder="Ej: 19.4326">
                    @error('latitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="longitud" class="form-label">Longitud</label>
                    <input type="number" step="any" name="longitud" id="longitud" class="form-control @error('longitud') is-invalid @enderror" 
                           value="{{ old('longitud', $sede->longitud) }}" placeholder="Ej: -99.1332">
                    @error('longitud')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label for="radio_geocerca_metros" class="form-label">Radio geocerca (metros)</label>
                    <input type="number" name="radio_geocerca_metros" id="radio_geocerca_metros" 
                           class="form-control @error('radio_geocerca_metros') is-invalid @enderror" 
                           value="{{ old('radio_geocerca_metros', $sede->radio_geocerca_metros ?? 500) }}" min="0">
                    @error('radio_geocerca_metros')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="geocerca_activa" id="geocerca_activa" class="form-check-input" value="1" 
                               {{ old('geocerca_activa', $sede->geocerca_activa) ? 'checked' : '' }}>
                        <label for="geocerca_activa" class="form-check-label">Activar geocerca (validación de ubicación)</label>
                    </div>
                </div>

                <!-- ========================================= -->
                <!-- 4. CONFIGURACIÓN ACADÉMICA               -->
                <!-- ========================================= -->

                <div class="col-md-3">
                    <label for="calificacion_minima" class="form-label">Calif. mínima</label>
                    <input type="number" step="0.01" name="calificacion_minima" id="calificacion_minima" 
                           class="form-control @error('calificacion_minima') is-invalid @enderror" 
                           value="{{ old('calificacion_minima', $sede->calificacion_minima ?? 6.00) }}" min="0" max="10">
                    @error('calificacion_minima')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="calificacion_maxima" class="form-label">Calif. máxima</label>
                    <input type="number" step="0.01" name="calificacion_maxima" id="calificacion_maxima" 
                           class="form-control @error('calificacion_maxima') is-invalid @enderror" 
                           value="{{ old('calificacion_maxima', $sede->calificacion_maxima ?? 10.00) }}" min="0" max="10">
                    @error('calificacion_maxima')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="tolerancia_retardo_minutos" class="form-label">Tolerancia retardo (min)</label>
                    <input type="number" name="tolerancia_retardo_minutos" id="tolerancia_retardo_minutos" 
                           class="form-control @error('tolerancia_retardo_minutos') is-invalid @enderror" 
                           value="{{ old('tolerancia_retardo_minutos', $sede->tolerancia_retardo_minutos ?? 10) }}" min="0">
                    @error('tolerancia_retardo_minutos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label for="moneda" class="form-label">Moneda</label>
                    <input type="text" name="moneda" id="moneda" class="form-control @error('moneda') is-invalid @enderror" 
                           value="{{ old('moneda', $sede->moneda ?? 'MXN') }}" maxlength="10">
                    @error('moneda')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="zona_horaria" class="form-label">Zona horaria</label>
                    <select name="zona_horaria" id="zona_horaria" class="form-control @error('zona_horaria') is-invalid @enderror">
                        <option value="America/Mexico_City" {{ old('zona_horaria', $sede->zona_horaria ?? 'America/Mexico_City') == 'America/Mexico_City' ? 'selected' : '' }}>Ciudad de México (CDMX)</option>
                        <option value="America/Monterrey" {{ old('zona_horaria', $sede->zona_horaria) == 'America/Monterrey' ? 'selected' : '' }}>Monterrey</option>
                        <option value="America/Guadalajara" {{ old('zona_horaria', $sede->zona_horaria) == 'America/Guadalajara' ? 'selected' : '' }}>Guadalajara</option>
                        <option value="America/Tijuana" {{ old('zona_horaria', $sede->zona_horaria) == 'America/Tijuana' ? 'selected' : '' }}>Tijuana</option>
                        <option value="America/Merida" {{ old('zona_horaria', $sede->zona_horaria) == 'America/Merida' ? 'selected' : '' }}>Mérida</option>
                        <option value="America/Chihuahua" {{ old('zona_horaria', $sede->zona_horaria) == 'America/Chihuahua' ? 'selected' : '' }}>Chihuahua</option>
                        <option value="America/Hermosillo" {{ old('zona_horaria', $sede->zona_horaria) == 'America/Hermosillo' ? 'selected' : '' }}>Hermosillo</option>
                    </select>
                    @error('zona_horaria')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="configuracion" class="form-label">Configuración extra (JSON)</label>
                    <textarea name="configuracion" id="configuracion" rows="2" 
                              class="form-control @error('configuracion') is-invalid @enderror" 
                              placeholder='{"clave":"valor", "otra":"config"}'>{{ old('configuracion', is_array($sede->configuracion) ? json_encode($sede->configuracion, JSON_PRETTY_PRINT) : $sede->configuracion) }}</textarea>
                    @error('configuracion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Opcional: ingresa un objeto JSON válido para configuraciones personalizadas.</small>
                </div>

                <!-- ========================================= -->
                <!-- 5. ESTADO                                -->
                <!-- ========================================= -->

                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="activa" id="activa" class="form-check-input" value="1" 
                               {{ old('activa', $sede->activa) ? 'checked' : '' }}>
                        <label for="activa" class="form-check-label">Sede activa</label>
                    </div>
                </div>

                <!-- ========================================= -->
                <!-- 6. BOTONES                              -->
                <!-- ========================================= -->
                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Actualizar Sede
                    </button>
                    <a href="{{ route('sedes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>

            </div><!-- /.row -->
        </form>
    </x-ui.card>
</div>
@endsection