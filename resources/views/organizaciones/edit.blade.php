<x-layouts.app page-title="Editar Organización">
<x-ui.page-header title="Editar Organización"
    :items="[['label'=>'Volver','url'=>route('organizaciones.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ isset($item) ? route('organizaciones.update',$item) : route('organizaciones.store') }}">
@csrf @method(isset($item) ? 'PUT' : 'POST')
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