<x-layouts.app page-title="Usuarios">
<x-ui.page-header title="Usuarios del sistema" subtitle="Gestión de usuarios, roles y sedes.">
    <x-slot name="actions">
        @can('usuarios.crear')
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">+ Nuevo usuario</a>
        @endcan
    </x-slot>
</x-ui.page-header>

<x-ui.filter-bar :action="route('users.index')">
    <x-slot name="fields">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control form-control-sm"
                   placeholder="Nombre, email o usuario..." value="{{ request('q') }}">
        </div>
        <div class="col-md-2">
            <select name="activo" class="form-select form-select-sm">
                <option value="">Estado</option>
                <option value="1" {{ request('activo')==='1'?'selected':'' }}>Activos</option>
                <option value="0" {{ request('activo')==='0'?'selected':'' }}>Inactivos</option>
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>

<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead>
                <tr>
                    <th>Usuario</th><th>Email</th><th>Roles</th>
                    <th>Sede principal</th><th>Estado</th><th>Último acceso</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <x-ui.avatar :name="$u->nombre_completo" size="sm" />
                            <div>
                                <div class="fw-medium" style="font-size:.875rem">{{ $u->nombre_completo }}</div>
                                <div class="text-muted" style="font-size:.75rem">@{{ $u->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.8rem">{{ $u->email }}</td>
                    <td>
                        @foreach($u->roles->take(2) as $r)
                        <x-ui.badge type="secondary" :small="true">{{ $r->nombre }}</x-ui.badge>
                        @endforeach
                        @if($u->roles->count() > 2)
                        <x-ui.badge type="secondary" small>+{{ $u->roles->count()-2 }}</x-ui.badge>
                        @endif
                    </td>
                    <td style="font-size:.8rem">{{ $u->sedePrincipal()?->nombre ?? '—' }}</td>
                    <td>
                        <x-ui.badge :type="$u->activo?'success':($u->estaBloqueado()?'danger':'secondary')">
                            {{ $u->activo ? ($u->estaBloqueado()?'Bloqueado':'Activo') : 'Inactivo' }}
                        </x-ui.badge>
                    </td>
                    <td style="font-size:.75rem">{{ $u->ultimo_acceso_at?->diffForHumans() ?? 'Nunca' }}</td>
                    <td class="text-end">
                        <a href="{{ route('users.show',$u) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @can('usuarios.editar')
                        <a href="{{ route('users.edit',$u) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin usuarios registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top d-flex justify-content-between align-items-center">
        <small class="text-muted">{{ $users->total() }} usuarios</small>
        {{ $users->links() }}
    </div>
</x-ui.card>
</x-layouts.app>
