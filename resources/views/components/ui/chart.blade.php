@props(['id'=>'chart-'.Str::random(6),'type'=>'bar','height'=>'280px','labels'=>[],'datasets'=>[],'title'=>null])
<div>
    @if($title)<p class="fw-semibold mb-2" style="font-size:.875rem">{{ $title }}</p>@endif
    <canvas id="{{ $id }}" style="max-height:{{ $height }}"></canvas>
</div>
@push('scripts')
<script>
(function(){
    const ctx = document.getElementById('{{ $id }}');
    if(!ctx) return;
    // Chart.js debe estar disponible (CDN o npm)
    if(typeof Chart === 'undefined') {
        ctx.parentElement.innerHTML = '<div class="alert alert-warning py-2" style="font-size:.8rem">Chart.js no cargado.</div>';
        return;
    }
    new Chart(ctx, {
        type: '{{ $type }}',
        data: {
            labels: @json($labels),
            datasets: @json($datasets),
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'bottom' } },
        }
    });
})();
</script>
@endpush
