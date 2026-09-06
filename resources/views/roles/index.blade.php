<x-layouts.app page-title="Roles">
<x-ui.page-header title="Roles del sistema" subtitle="Gestión de roles y asignación de permisos.">
    <x-slot name="actions">
        @can('roles.asignar')
        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">+ Nuevo rol</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr><th>Nombre</th><th>Slug</th><th>Nivel</th><th>Permisos</th><th>Usuarios</th><th>Sistema</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($roles as $rol)
                <tr>
                    <td class="fw-medium">{{ $rol->nombre }}</td>
                    <td><code style="font-size:.8rem">{{ $rol->slug }}</code></td>
                    <td class="text-center"><x-ui.badge type="secondary" small>{{ $rol->nivel }}</x-ui.badge></td>
                    <td class="text-center"><x-ui.badge type="info" small>{{ $rol->permissions_count }}</x-ui.badge></td>
                    <td class="text-center" style="font-size:.8rem">{{ $rol->users_count ?? 0 }}</td>
                    <td class="text-center">{{ $rol->es_sistema ? '<span title="No modificable">🔒</span>' : '' }}</td>
                    <td class="text-end">
                        <a href="{{ route('roles.show',$rol) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @if(!$rol->es_sistema)
                        @can('roles.asignar')
                        <a href="{{ route('roles.edit',$rol) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin roles." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $roles->links() }}</div>
</x-ui.card>
</x-layouts.app>
