<x-layouts.app page-title="Ciclo: {{ $ciclo->nombre }}">
<x-ui.page-header title="{{ $ciclo->nombre }}" :items="[['label'=>'Ciclos','url'=>route('ciclos.index')],['label'=>$ciclo->nombre]]">
    <x-slot name="actions">
        @can('ciclos.crear')
        <a href="{{ route('ciclos.edit', $ciclo) }}" class="btn btn-sm btn-outline-primary">Editar</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<div class="row g-3">
    <div class="col-md-6">
        <x-ui.card title="Información general">
            <dl class="row mb-0">
                <dt class="col-5 text-muted" style="font-size:.875rem">Escuela</dt>
                <dd class="col-7">{{ $ciclo->escuela->nombre }}</dd>
                <dt class="col-5 text-muted" style="font-size:.875rem">Inicio</dt>
                <dd class="col-7">{{ $ciclo->fecha_inicio->format('d/m/Y') }}</dd>
                <dt class="col-5 text-muted" style="font-size:.875rem">Fin</dt>
                <dd class="col-7">{{ $ciclo->fecha_fin->format('d/m/Y') }}</dd>
                <dt class="col-5 text-muted" style="font-size:.875rem">Estado</dt>
                <dd class="col-7">
                    <x-ui.badge :type="match($ciclo->estatus){ 'activo'=>'success','cerrado'=>'secondary', default=>'warning' }">
                        {{ ucfirst($ciclo->estatus) }}
                    </x-ui.badge>
                </dd>
                <dt class="col-5 text-muted" style="font-size:.875rem">Ciclo actual</dt>
                <dd class="col-7">{{ $ciclo->es_actual ? 'Sí' : 'No' }}</dd>
            </dl>
        </x-ui.card>
    </div>
    <div class="col-md-6">
        <x-ui.card title="Grupos en este ciclo">
            @if($ciclo->grupos->count())
            <ul class="list-group list-group-flush">
                @foreach($ciclo->grupos->take(10) as $g)
                <li class="list-group-item d-flex justify-content-between px-0" style="font-size:.875rem">
                    <span>{{ $g->nombre }} — {{ $g->grado->nombre ?? '' }}</span>
                    <x-ui.badge type="secondary" small>{{ $g->turno }}</x-ui.badge>
                </li>
                @endforeach
            </ul>
            @if($ciclo->grupos->count() > 10)
                <p class="text-muted mt-2 mb-0" style="font-size:.8rem">y {{ $ciclo->grupos->count() - 10 }} más...</p>
            @endif
            @else
            <x-ui.empty-state message="Sin grupos en este ciclo." />
            @endif
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
