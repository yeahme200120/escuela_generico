@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Grado: {{ $grado->nombre }}" subtitle="Detalles del grado" :actions="[
        ['label' => 'Editar', 'route' => route('grados.edit', $grado)],
        ['label' => 'Volver', 'route' => route('grados.index')]
    ]" />
    <x-ui.card>
        <dl class="row">
            <dt class="col-sm-3">ID</dt><dd class="col-sm-9">{{ $grado->id }}</dd>
            <dt class="col-sm-3">Nombre</dt><dd class="col-sm-9">{{ $grado->nombre }}</dd>
            <dt class="col-sm-3">Clave</dt><dd class="col-sm-9">{{ $grado->clave ?? '—' }}</dd>
            <dt class="col-sm-3">Nivel educativo</dt><dd class="col-sm-9">{{ $grado->nivelEducativo->nombre ?? 'N/A' }}</dd>
            <dt class="col-sm-3">Organización</dt><dd class="col-sm-9">{{ $grado->organizacion->nombre ?? 'N/A' }}</dd>
            <dt class="col-sm-3">Orden</dt><dd class="col-sm-9">{{ $grado->orden ?? '—' }}</dd>
            <dt class="col-sm-3">Estado</dt><dd class="col-sm-9"><x-ui.badge :type="$grado->activo ? 'success' : 'danger'">{{ $grado->activo ? 'Activo' : 'Inactivo' }}</x-ui.badge></dd>
            <dt class="col-sm-3">Creado</dt><dd class="col-sm-9">{{ $grado->created_at->format('d/m/Y H:i') }}</dd>
            <dt class="col-sm-3">Actualizado</dt><dd class="col-sm-9">{{ $grado->updated_at->format('d/m/Y H:i') }}</dd>
        </dl>
    </x-ui.card>
</div>
@endsection