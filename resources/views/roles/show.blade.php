<x-layouts.app page-title="Rol: {{ $rol->nombre }}">
<x-ui.page-header title="{{ $rol->nombre }}"
    :items="[['label'=>'Roles','url'=>route('roles.index')],['label'=>$rol->nombre]]">
    <x-slot name="actions">
        @if(!$rol->es_sistema)
        @can('roles.asignar')
        <a href="{{ route('roles.edit',$rol) }}" class="btn btn-sm btn-outline-primary">Editar</a>
        @endcan
        @endif
    </x-slot>
</x-ui.page-header>
<div class="row g-3">
    <div class="col-md-4">
        <x-ui.card title="Información">
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-5 text-muted">Slug</dt><dd class="col-7"><code>{{ $rol->slug }}</code></dd>
                <dt class="col-5 text-muted">Nivel</dt><dd class="col-7"><x-ui.badge type="secondary">{{ $rol->nivel }}</x-ui.badge></dd>
                <dt class="col-5 text-muted">Sistema</dt><dd class="col-7">{{ $rol->es_sistema ? '🔒 Sí' : 'No' }}</dd>
                <dt class="col-5 text-muted">Permisos</dt><dd class="col-7">{{ $rol->permissions->count() }}</dd>
                <dt class="col-5 text-muted">Usuarios</dt><dd class="col-7">{{ $rol->users->count() }}</dd>
            </dl>
            @if($rol->descripcion)
            <hr>
            <p class="text-muted mb-0" style="font-size:.875rem">{{ $rol->descripcion }}</p>
            @endif
        </x-ui.card>
    </div>
    <div class="col-md-8">
        <x-ui.card title="Permisos asignados" :flush="true">
            @php $porModulo = $rol->permissions->groupBy('modulo'); @endphp
            @if($porModulo->isNotEmpty())
            <div class="p-3">
                @foreach($porModulo as $modulo => $perms)
                <div class="mb-3">
                    <h6 class="fw-semibold text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.05em">{{ $modulo }}</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($perms as $p)
                        <x-ui.badge type="primary" small>{{ $p->accion }}</x-ui.badge>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <x-ui.empty-state message="Sin permisos asignados." />
            @endif
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
