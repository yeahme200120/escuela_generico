<x-layouts.app page-title="Tutores">
<x-ui.page-header title="Tutores"
    :items="[['label'=>'Tutores','url'=>route('tutores.index')],['label'=>'Detalle']]">
    <x-slot name="actions">
        <a href="{{ route('tutores.edit',$item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
    </x-slot>
</x-ui.page-header>
<x-ui.card>
    <dl class="row mb-0" style="font-size:.875rem">
        @foreach($item->toArray() as $k => $v)
        @if(!in_array($k,['id','created_at','updated_at','deleted_at']) && !is_array($v) && !is_null($v))
        <dt class="col-md-3 text-muted">{{ ucfirst(str_replace('_',' ',$k)) }}</dt>
        <dd class="col-md-9">{{ $v }}</dd>
        @endif
        @endforeach
    </dl>
</x-ui.card>
</x-layouts.app>