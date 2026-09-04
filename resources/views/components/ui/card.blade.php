@props([
    'title'  => null,
    'footer' => null,
    'flush'  => false,    // true = sin padding en el body
    'class'  => '',
])
<div {{ $attributes->merge(['class' => 'card border-0 shadow-sm '.$class]) }}>
    @if($title)
    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between py-3">
        <h6 class="mb-0 fw-semibold">{{ $title }}</h6>
        @isset($actions)<div class="d-flex gap-2">{{ $actions }}</div>@endisset
    </div>
    @endif
    <div class="{{ $flush ? 'card-body p-0' : 'card-body' }}">
        {{ $slot }}
    </div>
    @if($footer)
    <div class="card-footer bg-transparent border-top py-2 text-muted" style="font-size:.8rem">
        {{ $footer }}
    </div>
    @endif
</div>
