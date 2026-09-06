<x-layouts.app page-title="Historial académico">
<x-ui.page-header title="Historial académico — {{ $alumno->nombre_completo }}"
    :items="[['label'=>'Alumnos','url'=>route('alumnos.index')],['label'=>$alumno->nombre_completo,'url'=>route('alumnos.show',$alumno)],['label'=>'Historial']]" />

<x-ui.filter-bar :action="route('alumnos.show',$alumno)">
    <x-slot name="fields">
        <div class="col-md-3">
            <select name="ciclo_id" class="form-select form-select-sm">
                <option value="">Todos los ciclos</option>
                @foreach($ciclos as $c)
                <option value="{{ $c->id }}" {{ request('ciclo_id')==$c->id?'selected':'' }}>{{ $c->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="materia_id" class="form-select form-select-sm">
                <option value="">Todas las materias</option>
                @foreach($materias as $m)
                <option value="{{ $m->id }}" {{ request('materia_id')==$m->id?'selected':'' }}>{{ $m->nombre }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

@foreach($historial as $cicloNombre => $periodos)
<x-ui.card :title="$cicloNombre" class="mb-3" :flush="true">
    @foreach($periodos as $periodoNombre => $califs)
    <div class="px-3 py-2 bg-light border-bottom">
        <span class="fw-semibold" style="font-size:.875rem">{{ $periodoNombre }}</span>
        @php
        $aprobadas = $califs->where('resultado','aprobado')->count();
        $reprobadas = $califs->where('resultado','reprobado')->count();
        $promedio = $califs->whereNotNull('calificacion')->avg('calificacion');
        @endphp
        <span class="ms-3 text-muted" style="font-size:.78rem">
            Promedio: <strong>{{ $promedio ? number_format($promedio,1) : '—' }}</strong>
            · Aprobadas: <strong class="text-success">{{ $aprobadas }}</strong>
            · Reprobadas: <strong class="text-danger">{{ $reprobadas }}</strong>
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-se table-sm mb-0">
            <thead><tr><th>Materia</th><th>Calificación</th><th>Resultado</th><th>Docente</th></tr></thead>
            <tbody>
                @foreach($califs as $cal)
                <tr class="{{ $cal->resultado==='reprobado'?'table-danger bg-opacity-10':'' }}">
                    <td class="fw-medium" style="font-size:.875rem">{{ $cal->materia?->nombre }}</td>
                    <td class="fw-bold {{ $cal->resultado==='reprobado'?'text-danger':($cal->resultado==='aprobado'?'text-success':'') }}" style="font-size:1rem">
                        {{ $cal->calificacion !== null ? number_format($cal->calificacion,1) : 'NP' }}
                    </td>
                    <td>
                        <x-ui.badge :type="match($cal->resultado??'na'){'aprobado'=>'success','reprobado'=>'danger','np'=>'secondary','na'=>'secondary',default=>'secondary'}" small>
                            {{ strtoupper($cal->resultado ?? 'NP') }}
                        </x-ui.badge>
                    </td>
                    <td style="font-size:.8rem">{{ $cal->docente?->user?->nombres ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach
</x-ui.card>
@endforeach

@if($historial->isEmpty())
<x-ui.card><x-ui.empty-state message="Sin historial académico con los filtros aplicados." /></x-ui.card>
@endif
</x-layouts.app>
