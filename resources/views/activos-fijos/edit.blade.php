<x-layouts.app page-title="Editar Activo Fijo">
<x-ui.page-header title="Editar Activo Fijo"
    :items="[['label'=>'Volver','url'=>route('activos-fijos.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ isset($item) ? route('activos-fijos.update',$item) : route('activos-fijos.store') }}">
@csrf @method(isset($item) ? 'PUT' : 'POST')
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