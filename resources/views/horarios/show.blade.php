<x-layouts.app page-title="Horario">
<x-ui.page-header title="Detalle de horario"
    :items="[['label'=>'Horarios','url'=>route('horarios.index')],['label'=>'Detalle']]">
    <x-slot name="actions">
        @can('horarios.editar')
        <a href="{{ route('horarios.edit',$horario) }}" class="btn btn-sm btn-outline-primary">Editar</a>
        @endcan
        @if(!$horario->publicado)
        @can('horarios.publicar')
        <form method="POST" action="{{ route('horarios.publicar',$horario) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success ms-1">Publicar</button>
        </form>
        @endcan
        @endif
    </x-slot>
</x-ui.page-header>
<x-ui.card>
    <dl class="row mb-0" style="font-size:.875rem">
        <dt class="col-md-3 text-muted">Grupo</dt><dd class="col-md-9">{{ $horario->grupo?->nombre }}</dd>
        <dt class="col-md-3 text-muted">Materia</dt><dd class="col-md-9">{{ $horario->materia?->nombre }}</dd>
        <dt class="col-md-3 text-muted">Docente</dt><dd class="col-md-9">{{ $horario->docente?->user?->nombre_completo }}</dd>
        <dt class="col-md-3 text-muted">Aula</dt><dd class="col-md-9">{{ $horario->aula?->nombre ?? '—' }}</dd>
        <dt class="col-md-3 text-muted">Día</dt><dd class="col-md-9">{{ [1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes'][$horario->dia_semana] ?? '—' }}</dd>
        <dt class="col-md-3 text-muted">Hora inicio</dt><dd class="col-md-9">{{ substr($horario->hora_inicio,0,5) }}</dd>
        <dt class="col-md-3 text-muted">Hora fin</dt><dd class="col-md-9">{{ substr($horario->hora_fin,0,5) }}</dd>
        <dt class="col-md-3 text-muted">Publicado</dt><dd class="col-md-9">
            <x-ui.badge :type="$horario->publicado?'success':'warning'">{{ $horario->publicado?'Sí':'No' }}</x-ui.badge>
        </dd>
    </dl>
</x-ui.card>
</x-layouts.app>
