<x-layouts.app page-title="Nueva Operación">
<x-ui.page-header title="Nueva Operación"
    :items="[['label'=>'Volver','url'=>route('finanzas.pagos.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('finanzas.pagos.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
        {{ $slot ?? '' }}
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('finanzas.pagos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>