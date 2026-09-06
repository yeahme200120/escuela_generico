<x-layouts.app page-title="Ciclo Escolar">
<x-ui.page-header title="Ciclo Escolar"
    :items="[['label'=>'Ciclo Escolar','url'=>route('ciclos.index')],['label'=>'Detalle']]">
    <x-slot name="actions">
        <a href="{{ route('ciclos.edit',$item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
    </x-slot>
</x-ui.page-header>
<x-ui.card>
    <dl class="row mb-0" style="font-size:.875rem">
        @foreach(array_filter(
            (method_exists($item,'toArray') ? $item->toArray() : (array)$item),
            fn($k)=>!in_array($k,['id','created_at','updated_at','deleted_at']),
            ARRAY_FILTER_USE_KEY
        ) as $k=>$v)
        @if(!is_array($v) && !is_null($v))
        <dt class="col-md-4 text-muted">{{ ucfirst(str_replace('_',' ',$k)) }}</dt>
        <dd class="col-md-8">{{ $v }}</dd>
        @endif
        @endforeach
    </dl>
</x-ui.card>
</x-layouts.app>