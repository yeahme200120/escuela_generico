<x-layouts.app page-title="Inscribir Alumno">
<x-ui.page-header title="Inscribir Alumno"
    :items="[['label'=>'Volver','url'=>route('alumnos.inscripcion.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('alumnos.inscripcion.store') }}">
@csrf
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