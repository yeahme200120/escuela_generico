<x-layouts.app page-title="Nuevo Activo Fijo">
<x-ui.page-header title="Nuevo Activo Fijo"
    :items="[['label'=>'Volver','url'=>route('activos-fijos.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('activos-fijos.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
        {{ $slot ?? '' }}
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('activos-fijos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>