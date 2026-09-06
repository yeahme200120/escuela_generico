<x-layouts.app page-title="Calificaciones">
<x-ui.page-header title="Captura de calificaciones" subtitle="Selecciona grupo y periodo para gestionar calificaciones." />

<x-ui.filter-bar :action="route('calificaciones.index')">
    <x-slot name="fields">
        <div class="col-md-3">
            <label class="form-label form-label-sm mb-1">Grupo</label>
            <select name="grupo_id" class="form-select form-select-sm">
                <option value="">Seleccionar grupo...</option>
                @foreach($grupos as $g)
                <option value="{{ $g->id }}" {{ request('grupo_id')==$g->id?'selected':'' }}>
                    {{ $g->nombre }} — {{ $g->grado?->nombre }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label form-label-sm mb-1">Periodo</label>
            <select name="periodo_id" class="form-select form-select-sm">
                <option value="">Seleccionar periodo...</option>
                @foreach($periodos as $p)
                <option value="{{ $p->id }}" {{ request('periodo_id')==$p->id?'selected':'' }}>
                    {{ $p->nombre }} {{ $p->cerrado ? '🔒' : '' }}
                </option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

@if($alumnos->count() > 0)

{{-- Estadísticas rápidas --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <x-ui.stat-card label="Alumnos" :value="$alumnos->count()" color="primary" />
    </div>
    <div class="col-6 col-md-3">
        @php
        $calificadas = collect($calificaciones)->flatten()->count();
        $total = $alumnos->count() * $materias->count();
        $pct = $total > 0 ? round(($calificadas/$total)*100) : 0;
        @endphp
        <x-ui.stat-card label="% Capturado" :value="$pct.'%'" color="info" />
    </div>
    <div class="col-6 col-md-3">
        @php
        $reprobados = collect($calificaciones)->flatten()->where('resultado','reprobado')->count();
        @endphp
        <x-ui.stat-card label="Reprobados" :value="$reprobados" color="danger" />
    </div>
    <div class="col-6 col-md-3">
        @php $promG = collect($promedios)->filter()->avg(); @endphp
        <x-ui.stat-card label="Promedio grupo" :value="$promG ? number_format($promG,1) : '—'" color="success" />
    </div>
</div>

<x-ui.card :flush="true">
    @if($materias->count() === 0)
        <x-ui.empty-state message="No hay materias asignadas a este grupo en el ciclo actual." />
    @else
    <div class="table-responsive">
        <table class="table table-se table-sm mb-0">
            <thead>
                <tr>
                    <th style="min-width:200px">Alumno</th>
                    @foreach($materias as $m)
                    <th class="text-center" style="min-width:90px;font-size:.75rem">
                        {{ Str::limit($m->nombre, 14) }}
                    </th>
                    @endforeach
                    <th class="text-center" style="min-width:80px">Promedio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnos as $alumno)
                <tr>
                    <td style="font-size:.8rem">
                        <div class="fw-medium">{{ $alumno->apellido_paterno }} {{ $alumno->nombres }}</div>
                        <small class="text-muted">{{ $alumno->matricula }}</small>
                    </td>
                    @foreach($materias as $m)
                    @php $cal = $calificaciones[$alumno->id][$m->id] ?? null; @endphp
                    <td class="text-center p-1" style="font-size:.85rem">
                        @if($cal)
                            <span class="fw-semibold {{ $cal->resultado==='reprobado'?'text-danger':($cal->resultado==='aprobado'?'text-success':'') }}">
                                {{ $cal->calificacion !== null ? number_format($cal->calificacion,1) : '—' }}
                            </span>
                        @else
                            @can('calificaciones.registrar')
                            <a href="{{ route('calificaciones.create',['alumno_id'=>$alumno->id,'materia_id'=>$m->id,'periodo_id'=>request('periodo_id'),'grupo_id'=>request('grupo_id')]) }}"
                               class="btn btn-link btn-sm p-0 text-muted" title="Capturar calificación">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2z"/></svg>
                            </a>
                            @else
                            <span class="text-muted">—</span>
                            @endcan
                        @endif
                    </td>
                    @endforeach
                    <td class="text-center fw-semibold {{ ($promedios[$alumno->id] ?? 10) < 6 ? 'text-danger' : 'text-success' }}" style="font-size:.875rem">
                        {{ $promedios[$alumno->id] !== null ? number_format($promedios[$alumno->id],1) : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @can('calificaciones.cerrar')
    @php $periodo = $periodos->find(request('periodo_id')); @endphp
    @if($periodo && !$periodo->cerrado)
    <div class="px-3 py-2 border-top d-flex gap-2">
        <x-ui.confirm id="confirm-cerrar-periodo"
            title="¿Cerrar periodo de evaluación?"
            message="Una vez cerrado, solo usuarios con permiso especial podrán modificar calificaciones."
            :action="route('periodos-evaluacion.cerrar', request('periodo_id'))"
            label="Cerrar periodo" type="warning" :motivo="true">
            <x-slot name="trigger">
                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#confirm-cerrar-periodo">
                    🔒 Cerrar periodo
                </button>
            </x-slot>
        </x-ui.confirm>
    </div>
    @endif
    @endcan
    @endif
</x-ui.card>

@else
<x-ui.card>
    <x-ui.empty-state message="Selecciona un grupo y periodo para gestionar las calificaciones." />
</x-ui.card>
@endif
</x-layouts.app>
