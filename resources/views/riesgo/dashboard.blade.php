<x-layouts.app page-title="Dashboard de Riesgo Académico">
<x-ui.page-header title="Dashboard de Riesgo Académico — §75"
    subtitle="Clasificación de alumnos por nivel de riesgo académico." />

{{-- Filtros --}}
<x-ui.filter-bar :action="request()->url()">
    <x-slot name="fields">
        <div class="col-md-3">
            <select name="sede_id" class="form-select form-select-sm">
                <option value="">Sede</option>
                @foreach($sedes as $s)<option value="{{ $s->id }}" {{ request('sede_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="ciclo_id" class="form-select form-select-sm">
                <option value="">Ciclo escolar</option>
                @foreach($ciclos as $c)<option value="{{ $c->id }}" {{ request('ciclo_id')==$c->id?'selected':'' }}>{{ $c->nombre }}</option>@endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

{{-- Distribución por niveles --}}
<div class="row g-3 mb-4">
    @php
    $niveles = [
        'riesgo_alto'  => ['label'=>'Riesgo Alto',   'color'=>'danger',   'val'=>$distribucion['riesgo_alto'] ?? 0],
        'riesgo_medio' => ['label'=>'Riesgo Medio',  'color'=>'warning',  'val'=>$distribucion['riesgo_medio'] ?? 0],
        'observacion'  => ['label'=>'En Observación','color'=>'info',     'val'=>$distribucion['observacion'] ?? 0],
        'normal'       => ['label'=>'Normal',         'color'=>'success',  'val'=>$distribucion['normal'] ?? 0],
    ];
    $total = array_sum(array_column($niveles,'val'));
    @endphp
    @foreach($niveles as $key => $nivel)
    <div class="col-6 col-md-3">
        <x-ui.stat-card :label="$nivel['label']" :value="$nivel['val']" :color="$nivel['color']"
            :link="request()->fullUrlWithQuery(['nivel'=>$key])" />
    </div>
    @endforeach
</div>

{{-- Barras de distribución --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <x-ui.card title="Distribución por nivel">
            @foreach($niveles as $key => $nivel)
            @php $pct = $total > 0 ? round($nivel['val']/$total*100) : 0; @endphp
            <div class="d-flex align-items-center gap-3 py-2">
                <x-ui.badge :type="$nivel['color']" class="flex-shrink-0" style="min-width:130px;text-align:center">
                    {{ $nivel['label'] }}
                </x-ui.badge>
                <div class="flex-grow-1">
                    <div class="progress" style="height:10px">
                        <div class="progress-bar bg-{{ $nivel['color'] }}" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
                <span class="fw-semibold" style="min-width:45px;text-align:right">{{ $nivel['val'] }} <small class="text-muted fw-normal">({{ $pct }}%)</small></span>
            </div>
            @endforeach
        </x-ui.card>
    </div>
    <div class="col-md-6">
        <x-ui.card title="Indicadores generales">
            @if(!empty($indicadores))
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-7 text-muted">% Aprobación</dt>
                <dd class="col-5 fw-semibold text-success">{{ $indicadores['pct_aprobacion'] ?? '—' }}%</dd>
                <dt class="col-7 text-muted">% Reprobación</dt>
                <dd class="col-5 fw-semibold text-danger">{{ $indicadores['pct_reprobacion'] ?? '—' }}%</dd>
                <dt class="col-7 text-muted">% Deserción</dt>
                <dd class="col-5 fw-semibold text-warning">{{ $indicadores['pct_desercion'] ?? '—' }}%</dd>
                <dt class="col-7 text-muted">% Permanencia</dt>
                <dd class="col-5 fw-semibold text-primary">{{ $indicadores['pct_permanencia'] ?? '—' }}%</dd>
                <dt class="col-7 text-muted">Bajas temporales</dt>
                <dd class="col-5">{{ $indicadores['bajas_temporales'] ?? 0 }}</dd>
                <dt class="col-7 text-muted">Deserciones</dt>
                <dd class="col-5 text-danger">{{ $indicadores['deserciones'] ?? 0 }}</dd>
            </dl>
            @else
            <x-ui.empty-state message="Selecciona sede y ciclo para ver indicadores." />
            @endif
        </x-ui.card>
    </div>
</div>

{{-- Tabla de alumnos en riesgo --}}
<x-ui.card title="Alumnos en riesgo" :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Alumno</th><th>Grupo</th><th>Promedio</th><th>Asistencia</th><th>Reprobadas</th><th>Nivel riesgo</th><th></th></tr></thead>
            <tbody>
                @forelse($alumnosRiesgo as $a)
                <tr>
                    <td>
                        <div class="fw-medium" style="font-size:.875rem">{{ $a->nombre_completo }}</div>
                        <small class="text-muted">{{ $a->matricula }}</small>
                    </td>
                    <td style="font-size:.8rem">{{ $a->sedeActual?->nombre }}</td>
                    <td class="{{ ($a->promedio ?? 10) < 6 ? 'text-danger fw-bold' : '' }}" style="font-size:.875rem">
                        {{ $a->promedio !== null ? number_format($a->promedio,1) : '—' }}
                    </td>
                    <td style="font-size:.875rem">
                        {{ $a->pct_asistencia !== null ? round($a->pct_asistencia*100).'%' : '—' }}
                    </td>
                    <td class="{{ ($a->materias_reprobadas ?? 0) > 0 ? 'text-danger fw-bold' : '' }}" style="font-size:.875rem">
                        {{ $a->materias_reprobadas ?? 0 }}
                    </td>
                    <td>
                        <x-ui.badge :type="match($a->estatus_riesgo){'riesgo_alto'=>'danger','riesgo_medio'=>'warning','observacion'=>'info',default=>'success'}">
                            {{ ucfirst(str_replace('_',' ',$a->estatus_riesgo)) }}
                        </x-ui.badge>
                    </td>
                    <td><a href="{{ route('alumnos.show',$a) }}" class="btn btn-sm btn-outline-secondary">Ver</a></td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin alumnos en riesgo con los filtros aplicados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $alumnosRiesgo->links() }}</div>
</x-ui.card>
</x-layouts.app>
