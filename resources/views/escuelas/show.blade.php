@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header 
        title="Escuela: {{ $escuela->nombre }}" 
        subtitle="Detalles de la institución"
        :actions="[
            ['label' => 'Editar', 'route' => route('escuelas.edit', $escuela), 'icon' => 'edit'],
            ['label' => 'Volver', 'route' => route('escuelas.index'), 'icon' => 'arrow-left']
        ]"
    />

    <x-ui.card>
        <dl class="row">
            <dt class="col-sm-3">ID</dt>
            <dd class="col-sm-9">{{ $escuela->id }}</dd>

            <dt class="col-sm-3">Nombre</dt>
            <dd class="col-sm-9">{{ $escuela->nombre }}</dd>

            <dt class="col-sm-3">Clave</dt>
            <dd class="col-sm-9">{{ $escuela->clave ?? '—' }}</dd>

            <dt class="col-sm-3">Organización</dt>
            <dd class="col-sm-9">{{ $escuela->organizacion->nombre ?? 'N/A' }}</dd>

            <dt class="col-sm-3">Contacto</dt>
            <dd class="col-sm-9">
                @if($escuela->email)<i class="bi bi-envelope"></i> {{ $escuela->email }}<br>@endif
                @if($escuela->telefono)<i class="bi bi-telephone"></i> {{ $escuela->telefono }}@endif
            </dd>

            <dt class="col-sm-3">Dirección</dt>
            <dd class="col-sm-9">
                {{ $escuela->direccion ?? '—' }}<br>
                {{ $escuela->ciudad ?? '' }} {{ $escuela->estado ? ', '.$escuela->estado : '' }} {{ $escuela->codigo_postal ? ', CP '.$escuela->codigo_postal : '' }}
                <br>{{ $escuela->pais ?? 'México' }}
            </dd>

            <dt class="col-sm-3">Estado</dt>
            <dd class="col-sm-9">
                <x-ui.badge :type="$escuela->activa ? 'success' : 'danger'">
                    {{ $escuela->activa ? 'Activa' : 'Inactiva' }}
                </x-ui.badge>
            </dd>

            <dt class="col-sm-3">Creado</dt>
            <dd class="col-sm-9">{{ $escuela->created_at->format('d/m/Y H:i') }}</dd>

            <dt class="col-sm-3">Actualizado</dt>
            <dd class="col-sm-9">{{ $escuela->updated_at->format('d/m/Y H:i') }}</dd>
        </dl>
    </x-ui.card>
</div>
@endsection