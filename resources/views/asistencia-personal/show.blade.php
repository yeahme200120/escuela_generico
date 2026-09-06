<x-layouts.app page-title="Asistencia Personal">
<x-ui.page-header title="Asistencia Personal"
    :items="[['label'=>'Asistencia Personal','url'=>route('asistencia-personal.index')],['label'=>'Detalle']]">
    <x-slot name="actions">
        <a href="{{ route('asistencia-personal.edit',$item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
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