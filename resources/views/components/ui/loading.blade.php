@props(['text' => 'Cargando...', 'size' => 'sm', 'center' => true])
<div class="{{ $center ? 'd-flex justify-content-center align-items-center py-4' : 'd-inline-flex align-items-center gap-2' }}">
    <div class="spinner-border spinner-border-{{ $size }} text-primary" role="status" style="width:{{ $size==='sm'?'1rem':'2rem' }};height:{{ $size==='sm'?'1rem':'2rem' }}">
        <span class="visually-hidden">{{ $text }}</span>
    </div>
    @if($text)
    <span class="text-muted ms-2" style="font-size:.875rem">{{ $text }}</span>
    @endif
</div>
