<x-layouts.app page-title="Nueva Admisión">
<x-ui.page-header title="Nueva Admisión"
    :items="[['label'=>'Volver','url'=>route('admisiones.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('admisiones.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
        {{ $slot ?? '' }}
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('admisiones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>