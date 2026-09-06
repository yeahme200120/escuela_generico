@props([
    'type'  => 'secondary',
    'pill'  => false,
    'small' => false,
])
@php
$validTypes = ['primary','secondary','success','danger','warning','info','dark','light'];
$t = in_array($type, $validTypes) ? $type : 'secondary';
$cls = 'badge text-bg-'.$t.($pill?' rounded-pill':'').($small?' fw-normal':'');
@endphp
<span {{ $attributes->merge(['class' => $cls]) }}>{{ $slot }}</span>
