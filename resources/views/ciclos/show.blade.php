@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Ciclo Escolar: {{ $ciclo->nombre }}" subtitle="Detalles del periodo" :actions="[
        ['label' => 'Editar', 'route' => route('ciclos.edit', $ciclo)],
        ['label' => 'Volver', 'route' => route('ciclos.index')]
    ]" />
    <x-ui.card>
        <dl class="row">
            <dt class="col-sm-3">ID</dt><dd class="col-sm-9">{{ $ciclo->id }}</dd>
            <dt class="col-sm-3">Nombre</dt><dd class="col-sm-9">{{ $ciclo->nombre }}</dd>
            <dt class="col-sm-3">Clave</dt><dd class="col-sm-9">{{ $ciclo->clave ?? '—' }}</dd>
            <dt class="col-sm-3">Organización</dt><dd class="col-sm-9">{{ $ciclo->organizacion->nombre ?? 'N/A' }}</dd>
            <dt class="col-sm-3">Fecha de inicio</dt><dd class="col-sm-9">{{ $ciclo->fecha_inicio ? $ciclo->fecha_inicio->format('d/m/Y') : '—' }}</dd>
            <dt class="col-sm-3">Fecha de fin</dt><dd class="col-sm-9">{{ $ciclo->fecha_fin ? $ciclo->fecha_fin->format('d/m/Y') : '—' }}</dd>
            <dt class="col-sm-3">Ciclo actual</dt><dd class="col-sm-9">{{ $ciclo->es_actual ? 'Sí' : 'No' }}</dd>
            <dt class="col-sm-3">Estado</dt><dd class="col-sm-9"><x-ui.badge :type="$ciclo->activo ? 'success' : 'danger'">{{ $ciclo->activo ? 'Activo' : 'Inactivo' }}</x-ui.badge></dd>
            <dt class="col-sm-3">Creado</dt><dd class="col-sm-9">{{ $ciclo->created_at->format('d/m/Y H:i') }}</dd>
            <dt class="col-sm-3">Actualizado</dt><dd class="col-sm-9">{{ $ciclo->updated_at->format('d/m/Y H:i') }}</dd>
        </dl>
    </x-ui.card>
</div>
@endsection