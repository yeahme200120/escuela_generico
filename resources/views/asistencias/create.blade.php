<x-layouts.app page-title="Nueva Asistencia">
<x-ui.page-header title="Nueva Asistencia"
    :items="[['label'=>'Volver','url'=>route('asistencias.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('asistencias.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
        {{ $slot ?? '' }}
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('asistencias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>