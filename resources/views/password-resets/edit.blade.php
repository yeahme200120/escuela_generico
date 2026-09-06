<x-layouts.app page-title="Nueva Contraseña">
<x-ui.page-header title="Nueva Contraseña"
    :items="[['label'=>'Volver','url'=>route('password.reset.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ isset($item) ? route('password.reset.update',$item) : route('password.reset.store') }}">
@csrf @method(isset($item) ? 'PUT' : 'POST')
<x-ui.card title="Datos">
    <div class="row g-3">
        {{ $slot ?? '' }}
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('password.reset.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>