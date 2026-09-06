<x-layouts.app page-title="Nueva Organización">
<x-ui.page-header title="Nueva Organización"
    :items="[['label'=>'Volver','url'=>route('organizaciones.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('organizaciones.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
        {{ $slot ?? '' }}
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('organizaciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>