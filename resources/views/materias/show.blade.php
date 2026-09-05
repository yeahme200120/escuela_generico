@extends('components.layouts.app')

@section('content')
<div class="container-fluid">
    <x-ui.page-header title="Materia: {{ $materia->nombre }}" subtitle="Detalles de la materia" :actions="[
        ['label' => 'Editar', 'route' => route('materias.edit', $materia)],
        ['label' => 'Volver', 'route' => route('materias.index')]
    ]" />
    <x-ui.card>
        <dl class="row">
            <dt class="col-sm-3">ID</dt><dd class="col-sm-9">{{ $materia->id }}</dd>
            <dt class="col-sm-3">Clave</dt><dd class="col-sm-9">{{ $materia->clave }}</dd>
            <dt class="col-sm-3">Nombre</dt><dd class="col-sm-9">{{ $materia->nombre }}</dd>
            <dt class="col-sm-3">Escuela</dt><dd class="col-sm-9">{{ $materia->escuela->nombre ?? 'N/A' }}</dd>
            <dt class="col-sm-3">Tipo</dt><dd class="col-sm-9">{{ ucfirst($materia->tipo ?? '—') }}</dd>
            <dt class="col-sm-3">Horas por semana</dt><dd class="col-sm-9">{{ $materia->horas_semana ?? '—' }}</dd>
            <dt class="col-sm-3">Créditos</dt><dd class="col-sm-9">{{ $materia->creditos ?? '—' }}</dd>
            <dt class="col-sm-3">Descripción</dt><dd class="col-sm-9">{{ $materia->descripcion ?? '—' }}</dd>
            <dt class="col-sm-3">Estado</dt><dd class="col-sm-9"><x-ui.badge :type="$materia->activa ? 'success' : 'danger'">{{ $materia->activa ? 'Activa' : 'Inactiva' }}</x-ui.badge></dd>
            <dt class="col-sm-3">Creado</dt><dd class="col-sm-9">{{ $materia->created_at->format('d/m/Y H:i') }}</dd>
            <dt class="col-sm-3">Actualizado</dt><dd class="col-sm-9">{{ $materia->updated_at->format('d/m/Y H:i') }}</dd>
        </dl>
    </x-ui.card>
</div>
@endsection