<x-layouts.app page-title="Editar Asistencia">
<x-ui.page-header title="Editar Asistencia"
    :items="[['label'=>'Volver','url'=>route('asistencias.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ isset($item) ? route('asistencias.update',$item) : route('asistencias.store') }}">
@csrf @method(isset($item) ? 'PUT' : 'POST')
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