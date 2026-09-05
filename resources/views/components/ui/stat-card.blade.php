@props([
    'label'   => '',
    'value'   => '—',
    'icon'    => null,
    'color'   => 'primary',   // primary|success|warning|danger|info|secondary
    'trend'   => null,        // '+4.2%' o '-1.3%'
    'trendUp' => true,
    'link'    => null,
])
@php
$iconColors = [
    'primary'   => 'bg-primary bg-opacity-10 text-primary',
    'success'   => 'bg-success bg-opacity-10 text-success',
    'warning'   => 'bg-warning bg-opacity-10 text-warning',
    'danger'    => 'bg-danger bg-opacity-10 text-danger',
    'info'      => 'bg-info bg-opacity-10 text-info',
    'secondary' => 'bg-secondary bg-opacity-10 text-secondary',
];
$ic = $iconColors[$color] ?? $iconColors['primary'];
@endphp
<div class="stat-card {{ $link ? 'cursor-pointer' : '' }}" {{ $link ? "onclick=window.location.href='$link'" : '' }}>
    @if($icon)
    <div class="stat-icon {{ $ic }}">
        {!! $icon !!}
    </div>
    @endif
    <div class="stat-value">{{ $value }}</div>
    <div class="stat-label">{{ $label }}</div>
    @if($trend)
    <div class="mt-2" style="font-size:.78rem">
        <span class="fw-semibold {{ $trendUp ? 'text-success' : 'text-danger' }}">
            {{ $trendUp ? '↑' : '↓' }} {{ $trend }}
        </span>
        @isset($trendLabel)
        <span class="text-muted ms-1">{{ $trendLabel }}</span>
        @endisset
    </div>
    @endif
    @isset($footer)
    <div class="mt-2 pt-2 border-top" style="font-size:.78rem;color:var(--se-text-muted)">{{ $footer }}</div>
    @endisset
</div>
