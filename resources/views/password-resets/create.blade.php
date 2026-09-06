<x-layouts.app page-title="Recuperar Contraseña">
<x-ui.page-header title="Recuperar Contraseña"
    :items="[['label'=>'Volver','url'=>route('password.request.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('password.request.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
        {{ $slot ?? '' }}
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('password.request.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>