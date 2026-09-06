<x-layouts.app page-title="Kardex — {{ $alumno->nombre_completo }}">
<x-ui.page-header title="Kardex — {{ $alumno->nombre_completo }}"
    :items="[['label'=>'Alumnos','url'=>route('alumnos.index')],['label'=>$alumno->nombre_completo,'url'=>route('alumnos.show',$alumno)],['label'=>'Kardex']]">
    <x-slot name="actions">
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
            🖨 Imprimir
        </button>
    </x-slot>
</x-ui.page-header>

<div id="kardex-print">
    {{-- Encabezado institucional --}}
    <x-ui.card class="mb-3">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="fw-bold mb-1">{{ config('app.name') }}</h5>
                <p class="mb-1 text-muted" style="font-size:.875rem">KARDEX ACADÉMICO OFICIAL</p>
            </div>
            <div class="col-md-4 text-md-end">
                <p class="mb-0 fw-medium">{{ $alumno->nombre_completo }}</p>
                <p class="mb-0 text-muted" style="font-size:.875rem">Matrícula: {{ $alumno->matricula ?? '—' }}</p>
                <p class="mb-0 text-muted" style="font-size:.8rem">CURP: {{ $alumno->curp ?? '—' }}</p>
            </div>
        </div>
    </x-ui.card>

    {{-- Tabla general de calificaciones --}}
    @foreach($kardexPorCiclo as $ciclo => $materias)
    <x-ui.card :title="$ciclo" class="mb-3" :flush="true">
        <div class="table-responsive">
            <table class="table table-se table-sm mb-0">
                <thead>
                    <tr>
                        <th>Materia</th>
                        @foreach($periodos as $p)
                        <th class="text-center">{{ $p }}</th>
                        @endforeach
                        <th class="text-center">Promedio</th>
                        <th class="text-center">Resultado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materias as $materia => $califs)
                    @php
                    $vals = collect($califs)->whereNotNull('calificacion')->pluck('calificacion');
                    $prom = $vals->count() ? round($vals->avg(),1) : null;
                    $aprobada = $prom !== null && $prom >= 6.0;
                    @endphp
                    <tr class="{{ !$aprobada && $prom !== null ? 'table-danger bg-opacity-10' : '' }}">
                        <td class="fw-medium" style="font-size:.8rem">{{ $materia }}</td>
                        @foreach($periodos as $per)
                        @php $cal = $califs[$per] ?? null; @endphp
                        <td class="text-center {{ ($cal && $cal['resultado']==='reprobado')?'text-danger fw-bold':($cal?'text-success':'text-muted') }}" style="font-size:.875rem">
                            {{ $cal ? ($cal['calificacion'] !== null ? number_format($cal['calificacion'],1) : 'NP') : '—' }}
                        </td>
                        @endforeach
                        <td class="text-center fw-bold {{ $aprobada?'text-success':'text-danger' }}">
                            {{ $prom !== null ? number_format($prom,1) : '—' }}
                        </td>
                        <td class="text-center">
                            @if($prom !== null)
                            <x-ui.badge :type="$aprobada?'success':'danger'" small>{{ $aprobada?'ACRED':'REP' }}</x-ui.badge>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-ui.card>
    @endforeach

    @if(empty($kardexPorCiclo))
    <x-ui.card><x-ui.empty-state message="Sin calificaciones registradas." /></x-ui.card>
    @endif
</div>

@push('styles')
<style>
@media print {
    #sidebar, #topbar, .btn, nav, .breadcrumb { display: none !important; }
    #main-content { margin: 0 !important; }
    #kardex-print { font-size: 12px; }
}
</style>
@endpush
</x-layouts.app>
