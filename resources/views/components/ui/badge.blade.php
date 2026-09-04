@props([
    'type'  => 'secondary',  // primary|secondary|success|danger|warning|info|dark|light
    'pill'  => false,
    'small' => false,
])
<span {{ $attributes->merge([
    'class' => 'badge text-bg-'.$type.($pill ? ' rounded-pill' : '').($small ? ' fw-normal' : '')
]) }}>{{ $slot }}</span>
