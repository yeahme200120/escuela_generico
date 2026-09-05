@props(['items' => []])
{{-- items: [['label'=>'Inicio','url'=>'/'],['label'=>'Alumnos','url'=>'/alumnos'],['label'=>'Nuevo']] --}}
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0" style="font-size:.8rem">
        @foreach($items as $item)
        @if(!$loop->last)
            <li class="breadcrumb-item">
                <a href="{{ $item['url'] }}" class="text-decoration-none">{{ $item['label'] }}</a>
            </li>
        @else
            <li class="breadcrumb-item active" aria-current="page">{{ $item['label'] }}</li>
        @endif
        @endforeach
    </ol>
</nav>
