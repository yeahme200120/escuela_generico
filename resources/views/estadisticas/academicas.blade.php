<x-layouts.app page-title="Estadísticas Académicas">
<x-ui.page-header title="Estadísticas académicas — §74"
    subtitle="Indicadores de aprovechamiento por sede, ciclo, nivel, grado, grupo, materia y docente." />

<x-ui.filter-bar :action="request()->url()">
    <x-slot name="fields">
        <div class="col-md-2">
            <select name="sede_id" class="form-select form-select-sm">
                <option value="">Sede</option>
                @foreach($sedes as $s)<option value="{{ $s->id }}" {{ request('sede_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="ciclo_id" class="form-select form-select-sm">
                <option value="">Ciclo</option>
                @foreach($ciclos as $c)<option value="{{ $c->id }}" {{ request('ciclo_id')==$c->id?'selected':'' }}>{{ $c->nombre }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="grado_id" class="form-select form-select-sm">
                <option value="">Grado</option>
                @foreach($grados as $g)<option value="{{ $g->id }}" {{ request('grado_id')==$g->id?'selected':'' }}>{{ $g->nombre }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="materia_id" class="form-select form-select-sm">
                <option value="">Materia</option>
                @foreach($materias as $m)<option value="{{ $m->id }}" {{ request('materia_id')==$m->id?'selected':'' }}>{{ $m->nombre }}</option>@endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

@if(!empty($indicadores))
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><x-ui.stat-card label="Alumnos inscritos" :value="$indicadores['inscritos'] ?? '—'" color="primary" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="% Aprobación" :value="($indicadores['pct_aprobacion'] ?? '—').'%'" color="success" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="% Reprobación" :value="($indicadores['pct_reprobacion'] ?? '—').'%'" color="danger" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="% Deserción" :value="($indicadores['pct_desercion'] ?? '—').'%'" color="warning" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="% Permanencia" :value="($indicadores['pct_permanencia'] ?? '—').'%'" color="info" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="Bajas definitivas" :value="$indicadores['bajas_definitivas'] ?? 0" color="danger" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="Deserciones" :value="$indicadores['deserciones'] ?? 0" color="warning" /></div>
    <div class="col-6 col-md-3"><x-ui.stat-card label="Egresados" :value="$indicadores['egresados'] ?? 0" color="success" /></div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <x-ui.card title="Indicadores de permanencia">
            @php
            $items = [
                ['k'=>'pct_aprobacion','l'=>'% Aprobación','c'=>'success'],
                ['k'=>'pct_reprobacion','l'=>'% Reprobación','c'=>'danger'],
                ['k'=>'pct_desercion','l'=>'% Deserción','c'=>'warning'],
                ['k'=>'pct_permanencia','l'=>'% Permanencia','c'=>'primary'],
            ];
            @endphp
            @foreach($items as $it)
            @php $val = $indicadores[$it['k']] ?? 0; @endphp
            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                <span class="text-muted" style="width:130px;font-size:.8rem">{{ $it['l'] }}</span>
                <div class="flex-grow-1">
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-{{ $it['c'] }}" style="width:{{ min(100,$val) }}%"></div>
                    </div>
                </div>
                <span class="fw-semibold" style="width:50px;text-align:right;font-size:.875rem">{{ $val }}%</span>
            </div>
            @endforeach
        </x-ui.card>
    </div>
    <div class="col-md-6">
        <x-ui.card title="Calificaciones por materia" :flush="true">
            <div class="table-responsive">
                <table class="table table-se table-sm mb-0">
                    <thead><tr><th>Materia</th><th>Promedio</th><th>Aprobados</th><th>Reprobados</th></tr></thead>
                    <tbody>
                        @forelse($porMateria ?? [] as $mat)
                        <tr>
                            <td style="font-size:.8rem">{{ $mat->nombre }}</td>
                            <td class="{{ ($mat->promedio ?? 10) < 6 ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                                {{ $mat->promedio !== null ? number_format($mat->promedio,1) : '—' }}
                            </td>
                            <td class="text-success">{{ $mat->aprobados ?? 0 }}</td>
                            <td class="text-danger">{{ $mat->reprobados ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4"><x-ui.empty-state message="Sin datos." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
@else
<x-ui.card>
    <x-ui.empty-state message="Selecciona sede y ciclo para ver estadísticas académicas." />
</x-ui.card>
@endif
</x-layouts.app>
