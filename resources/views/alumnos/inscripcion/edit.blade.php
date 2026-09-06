<x-layouts.app page-title="Editar Inscripción">
<x-ui.page-header title="Editar Inscripción"
    :items="[['label'=>'Volver','url'=>route('alumnos.inscripcion.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ isset($item) ? route('alumnos.inscripcion.update',$item) : route('alumnos.inscripcion.store') }}">
@csrf @method(isset($item) ? 'PUT' : 'POST')
<x-ui.card title="Datos">
    <div class="row g-3">
        {{ $slot ?? '' }}
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('alumnos.inscripcion.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>