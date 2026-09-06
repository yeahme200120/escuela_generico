<x-layouts.app page-title="Horarios">
<x-ui.page-header title="Horarios" subtitle="Cuadrícula semanal por grupo. §36">
    <x-slot name="actions">
        @can('horarios.crear')
        <a href="{{ route('horarios.create') }}" class="btn btn-primary btn-sm">+ Nuevo horario</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="route('horarios.index')">
    <x-slot name="fields">
        <div class="col-md-3">
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
            <select name="ciclo_id" class="form-select form-select-sm">
                <option value="">Ciclo escolar</option>
                @foreach($ciclos as $c)
                <option value="{{ $c->id }}" {{ request('ciclo_id')==$c->id?'selected':'' }}>{{ $c->nombre }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

@if(request('grupo_id'))
{{-- Cuadrícula semanal §36 --}}
@php
$dias = [1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes'];
$porDia = $horarios->groupBy('dia_semana');
@endphp
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-bordered mb-0 text-center" style="font-size:.8rem">
            <thead>
                <tr>
                    <th style="width:80px">Horario</th>
                    @foreach($dias as $d)<th>{{ $d }}</th>@endforeach
                </tr>
            </thead>
            <tbody>
                @php
                $horas = ['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00'];
                @endphp
                @foreach($horas as $hora)
                <tr>
                    <td class="text-muted fw-medium" style="font-size:.75rem">{{ $hora }}</td>
                    @foreach(array_keys($dias) as $dia)
                    @php
                    $bloque = ($porDia[$dia] ?? collect())->first(fn($h) => substr($h->hora_inicio,0,5) === $hora);
                    @endphp
                    <td class="{{ $bloque ? 'bg-primary bg-opacity-10 border-primary' : '' }}" style="height:50px;vertical-align:middle">
                        @if($bloque)
                        <div class="fw-semibold text-primary" style="font-size:.78rem">{{ $bloque->materia?->nombre }}</div>
                        <div class="text-muted" style="font-size:.72rem">{{ $bloque->docente?->user?->nombres }}</div>
                        @if($bloque->aula)<div style="font-size:.7rem">{{ $bloque->aula?->nombre }}</div>@endif
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-ui.card>
@else
{{-- Lista de horarios sin filtro --}}
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Grupo</th><th>Materia</th><th>Docente</th><th>Día</th><th>Horario</th><th>Publicado</th><th></th></tr></thead>
            <tbody>
                @php $dias = [1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie']; @endphp
                @forelse($horarios as $h)
                <tr>
                    <td style="font-size:.875rem">{{ $h->grupo?->nombre }}</td>
                    <td style="font-size:.875rem">{{ $h->materia?->nombre }}</td>
                    <td style="font-size:.8rem">{{ $h->docente?->user?->nombres }}</td>
                    <td><x-ui.badge type="secondary" small>{{ $dias[$h->dia_semana] ?? $h->dia_semana }}</x-ui.badge></td>
                    <td style="font-size:.8rem">{{ substr($h->hora_inicio,0,5) }} – {{ substr($h->hora_fin,0,5) }}</td>
                    <td>
                        @if($h->publicado)
                            <x-ui.badge type="success" small>✓ Publicado</x-ui.badge>
                        @else
                            <x-ui.badge type="warning" small>Borrador</x-ui.badge>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('horarios.edit',$h) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                        @if(!$h->publicado)
                        @can('horarios.publicar')
                        <form method="POST" action="{{ route('horarios.publicar',$h) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-success ms-1">Publicar</button>
                        </form>
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin horarios registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $horarios->links() }}</div>
</x-ui.card>
@endif
</x-layouts.app>
