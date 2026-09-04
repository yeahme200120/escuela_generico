@props(['title', 'subtitle' => null])
<div class="d-flex align-items-start justify-content-between mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--se-text)">{{ $title }}</h4>
        @if($subtitle)
            <p class="text-muted mb-0" style="font-size:.875rem">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
    <div class="d-flex gap-2 flex-shrink-0">{{ $actions }}</div>
    @endisset
</div>
