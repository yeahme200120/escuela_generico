<x-layouts.app page-title="Dashboard">

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="mb-1 fw-bold">Bienvenido, {{ auth()->user()->nombres }}</h4>
        <p class="mb-0 text-muted" style="font-size:.875rem">
            {{ auth()->user()->roles->first()?->nombre ?? 'Usuario' }}
            @if($ciclo) · Ciclo: <strong>{{ $ciclo->nombre }}</strong> @endif
        </p>
    </div>
    <span class="badge text-bg-secondary">{{ now()->format('d/m/Y') }}</span>
</div>

{{-- Stat cards con indicadores reales --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <x-ui.stat-card
            label="Alumnos activos"
            :value="$indicadores['inscritos'] ?? '—'"
            color="primary"
            :link="route('alumnos.index')" />
    </div>
    <div class="col-6 col-xl-3">
        <x-ui.stat-card
            label="% Aprobación"
            :value="isset($indicadores['pct_aprobacion']) ? $indicadores['pct_aprobacion'].'%' : '—'"
            color="success" />
    </div>
    <div class="col-6 col-xl-3">
        @php $enRiesgo = ($riesgo['riesgo_alto'] ?? 0) + ($riesgo['riesgo_medio'] ?? 0); @endphp
        <x-ui.stat-card
            label="En riesgo académico"
            :value="$enRiesgo > 0 ? $enRiesgo : '—'"
            color="danger"
            :link="route('alumnos.index')" />
    </div>
    <div class="col-6 col-xl-3">
        <x-ui.stat-card
            label="% Deserción"
            :value="isset($indicadores['pct_desercion']) ? $indicadores['pct_desercion'].'%' : '—'"
            color="warning" />
    </div>
</div>

<div class="row g-3">
    {{-- Indicadores de permanencia §30 --}}
    @if(!empty($indicadores))
    <div class="col-md-6">
        <x-ui.card title="Indicadores de permanencia">
            <div class="row g-2">
                @php
                $items = [
                    ['label'=>'Inscritos',      'val'=>$indicadores['inscritos']      ?? 0, 'color'=>'primary'],
                    ['label'=>'Bajas temp.',     'val'=>$indicadores['bajas_temporales']  ?? 0, 'color'=>'warning'],
                    ['label'=>'Bajas def.',      'val'=>$indicadores['bajas_definitivas'] ?? 0, 'color'=>'danger'],
                    ['label'=>'Deserciones',     'val'=>$indicadores['deserciones']    ?? 0, 'color'=>'danger'],
                    ['label'=>'Egresados',       'val'=>$indicadores['egresados']      ?? 0, 'color'=>'success'],
                    ['label'=>'% Permanencia',   'val'=>($indicadores['pct_permanencia'] ?? 0).'%', 'color'=>'info'],
                ];
                @endphp
                @foreach($items as $it)
                <div class="col-6 col-md-4">
                    <div class="border rounded p-2 text-center">
                        <div class="fw-bold text-{{ $it['color'] }}" style="font-size:1.2rem">{{ $it['val'] }}</div>
                        <div class="text-muted" style="font-size:.75rem">{{ $it['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </x-ui.card>
    </div>
    @endif

    {{-- Riesgo académico §75 --}}
    @if(!empty($riesgo))
    <div class="col-md-6">
        <x-ui.card title="Dashboard de riesgo académico">
            @php
            $totalR = array_sum($riesgo);
            $niveles = [
                'riesgo_alto'  => ['label'=>'Riesgo alto',    'color'=>'danger'],
                'riesgo_medio' => ['label'=>'Riesgo medio',   'color'=>'warning'],
                'observacion'  => ['label'=>'En observación', 'color'=>'info'],
                'normal'       => ['label'=>'Normal',         'color'=>'success'],
            ];
            @endphp
            @foreach($niveles as $key => $meta)
            @php $val = $riesgo[$key] ?? 0; $pct = $totalR > 0 ? round(($val/$totalR)*100) : 0; @endphp
            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                <x-ui.badge :type="$meta['color']" class="flex-shrink-0" style="min-width:110px;text-align:center">
                    {{ $meta['label'] }}
                </x-ui.badge>
                <div class="flex-grow-1">
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-{{ $meta['color'] }}" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                <span class="fw-semibold" style="min-width:30px;text-align:right">{{ $val }}</span>
            </div>
            @endforeach
        </x-ui.card>
    </div>
    @endif

    {{-- Accesos recientes (trazabilidad) --}}
    @can('auditoria.ver')
    <div class="col-md-6">
        <x-ui.card title="Últimos accesos" :flush="true">
            <div class="table-responsive">
                <table class="table table-se table-sm mb-0">
                    <thead><tr><th>Usuario</th><th>Evento</th><th>IP</th><th>Geo</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @foreach(\App\Models\AccessLog::where('organizacion_id',auth()->user()->organizacion_id)->orderByDesc('created_at')->limit(8)->get() as $a)
                        <tr class="{{ $a->tieneAnomalias()?'table-warning':'' }}">
                            <td style="font-size:.78rem">{{ $a->email_intento ?? $a->user?->email ?? '—' }}</td>
                            <td><x-ui.badge :type="$a->resultado==='success'?'success':'danger'" small>{{ $a->evento }}</x-ui.badge></td>
                            <td style="font-size:.78rem">{{ $a->ip_address }}</td>
                            <td style="font-size:.75rem">
                                @if($a->latitud)
                                <span class="text-success" title="{{$a->latitud}},{{$a->longitud}}">📍</span>
                                @else —
                                @endif
                            </td>
                            <td style="font-size:.75rem">{{ $a->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-3 py-2 border-top">
                <a href="{{ route('auditoria.accesos') }}" class="btn btn-link btn-sm p-0" style="font-size:.8rem">Ver todos →</a>
            </div>
        </x-ui.card>
    </div>
    @endcan

    {{-- Info del sistema --}}
    <div class="col-md-6">
        <x-ui.card title="Estado del sistema">
            <div class="d-flex flex-wrap gap-2">
                <span class="badge text-bg-success">✓ BD: 44 migraciones</span>
                <span class="badge text-bg-success">✓ 69 modelos</span>
                <span class="badge text-bg-success">✓ RBAC multirol</span>
                <span class="badge text-bg-success">✓ Trazabilidad geo</span>
                <span class="badge text-bg-success">✓ Bootstrap 5</span>
                <span class="badge text-bg-warning text-dark">⏳ Vistas en desarrollo</span>
                <span class="badge text-bg-secondary">🔲 Python/FastAPI</span>
            </div>
            @if(config('app.debug'))
            <hr>
            <small class="text-muted d-block">Request ID: <code>{{ app(\App\Support\RequestContext::class)->getRequestId() }}</code></small>
            @endif
        </x-ui.card>
    </div>
</div>

</x-layouts.app>
