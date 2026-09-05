@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Sede: {{ $sede->nombre }}" 
        subtitle="Detalles completos"
        :actions="[
            ['label' => 'Editar', 'route' => route('sedes.edit', $sede), 'icon' => 'edit'],
            ['label' => 'Volver', 'route' => route('sedes.index'), 'icon' => 'arrow-left']
        ]"
    />

    <x-ui.card>
        <dl class="row">
            <dt class="col-sm-3">ID</dt>
            <dd class="col-sm-9">{{ $sede->id }}</dd>

            <dt class="col-sm-3">Nombre</dt>
            <dd class="col-sm-9">{{ $sede->nombre }}</dd>

            <dt class="col-sm-3">Clave</dt>
            <dd class="col-sm-9">{{ $sede->clave ?? '—' }}</dd>

            <dt class="col-sm-3">Escuela</dt>
            <dd class="col-sm-9">{{ $sede->escuela->nombre ?? 'N/A' }}</dd>

            <dt class="col-sm-3">Contacto</dt>
            <dd class="col-sm-9">
                @if($sede->email)<i class="bi bi-envelope"></i> {{ $sede->email }}<br>@endif
                @if($sede->telefono)<i class="bi bi-telephone"></i> {{ $sede->telefono }}@endif
            </dd>

            <dt class="col-sm-3">Dirección</dt>
            <dd class="col-sm-9">
                {{ $sede->direccion ?? '—' }}<br>
                {{ $sede->ciudad ?? '' }} {{ $sede->estado ? ', '.$sede->estado : '' }} {{ $sede->codigo_postal ? ', CP '.$sede->codigo_postal : '' }}
                <br>{{ $sede->pais ?? 'México' }}
            </dd>

            <dt class="col-sm-3">Geolocalización</dt>
            <dd class="col-sm-9">
                @if($sede->latitud && $sede->longitud)
                    Lat: {{ $sede->latitud }}, Lon: {{ $sede->longitud }}
                    @if($sede->geocerca_activa)
                        <span class="badge bg-success">Geocerca activa ({{ $sede->radio_geocerca_metros }}m)</span>
                    @else
                        <span class="badge bg-secondary">Geocerca inactiva</span>
                    @endif
                @else
                    <span class="text-muted">Sin coordenadas</span>
                @endif
            </dd>

            <dt class="col-sm-3">Configuración académica</dt>
            <dd class="col-sm-9">
                Calificación mínima: <strong>{{ $sede->calificacion_minima }}</strong><br>
                Calificación máxima: <strong>{{ $sede->calificacion_maxima }}</strong><br>
                Tolerancia retardo: <strong>{{ $sede->tolerancia_retardo_minutos }} minutos</strong><br>
                Zona horaria: <strong>{{ $sede->zona_horaria }}</strong><br>
                Moneda: <strong>{{ $sede->moneda }}</strong>
            </dd>

            <dt class="col-sm-3">Estado</dt>
            <dd class="col-sm-9">
                <x-ui.badge :type="$sede->activa ? 'success' : 'danger'">
                    {{ $sede->activa ? 'Activa' : 'Inactiva' }}
                </x-ui.badge>
            </dd>

            <dt class="col-sm-3">Creado</dt>
            <dd class="col-sm-9">{{ $sede->created_at->format('d/m/Y H:i') }}</dd>

            <dt class="col-sm-3">Actualizado</dt>
            <dd class="col-sm-9">{{ $sede->updated_at->format('d/m/Y H:i') }}</dd>
        </dl>
    </x-ui.card>
</div>
@endsection