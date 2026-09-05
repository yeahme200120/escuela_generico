<x-layouts.app page-title="{{ $nivel->nombre }}">
<x-ui.page-header title="{{ $nivel->nombre }}" :items="[['label'=>'Niveles','url'=>route('niveles.index')],['label'=>$nivel->nombre]]">
    <x-slot name="actions">
        <a href="{{ route('niveles.edit', $nivel) }}" class="btn btn-sm btn-outline-primary">Editar</a>
    </x-slot>
</x-ui.page-header>
<x-ui.card title="Grados de este nivel">
    @if($nivel->grados->count())
    <ul class="list-group list-group-flush">
        @foreach($nivel->grados->sortBy('orden') as $g)
        <li class="list-group-item px-0 d-flex justify-content-between" style="font-size:.875rem">
            <span>{{ $g->nombre }}</span>
            <x-ui.badge :type="$g->activo?'success':'secondary'" small>{{ $g->activo?'Activo':'Inactivo' }}</x-ui.badge>
        </li>
        @endforeach
    </ul>
    @else
    <x-ui.empty-state message="Sin grados configurados." />
    @endif
</x-ui.card>
</x-layouts.app>
