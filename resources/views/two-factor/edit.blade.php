<x-layouts.app page-title="Configurar 2FA">
<x-ui.page-header title="Configurar 2FA"
    :items="[['label'=>'Volver','url'=>route('two-factor.index')],['label'=>'Editar']]" />
<form method="POST" action="{{ isset($item) ? route('two-factor.update',$item) : route('two-factor.store') }}">
@csrf @method(isset($item) ? 'PUT' : 'POST')
<x-ui.card title="Datos">
    <div class="row g-3">
        {{ $slot ?? '' }}
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('two-factor.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>