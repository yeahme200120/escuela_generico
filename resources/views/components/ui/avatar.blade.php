@props(['name'=>'?','src'=>null,'size'=>'md','color'=>'primary'])
@php
$sizes=['sm'=>'28px','md'=>'36px','lg'=>'48px','xl'=>'64px'];
$s=$sizes[$size]??$sizes['md'];
$initials=collect(explode(' ',trim($name)))->map(fn($w)=>strtoupper(substr($w,0,1)))->take(2)->implode('');
@endphp
@if($src)
    <img src="{{ $src }}" alt="{{ $name }}" class="rounded-circle object-fit-cover" style="width:{{$s}};height:{{$s}}">
@else
    <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
         style="width:{{$s}};height:{{$s}};background:var(--se-{{$color}});font-size:calc({{$s}} * 0.38)">
        {{ $initials ?: '?' }}
    </div>
@endif
