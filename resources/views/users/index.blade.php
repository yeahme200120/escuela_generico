@extends('components.layouts.app')
@section('content')
<x-ui.page-header title="Usuarios" subtitle="Gestión de cuentas de usuario">
    <a href="{{ route('users.create') }}" class="btn btn-primary">+ Nuevo Usuario</a>
</x-ui.page-header>
<x-ui.card>
    <x-ui.filter-bar action="{{ route('users.index') }}" method="GET">
        <input type="text" name="search" placeholder="Buscar por nombre o email" class="form-control" />
    </x-ui.filter-bar>
    <x-ui.table>
        @slot('thead')
            <tr>
                <th>Email</th>
                <th>Nombre</th>
                <th>Roles</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        @endslot
        @foreach($items ?? [] as $user)
            <tr>
                <td><strong>{{ $user->email }}</strong></td>
                <td>{{ $user->nombre }}</td>
                <td>
                    @foreach($user->roles ?? [] as $role)
                        <x-ui.badge type="info">{{ $role->nombre }}</x-ui.badge>
                    @endforeach
                </td>
                <td>
                    @if($user->activo)
                        <x-ui.badge type="success">Activo</x-ui.badge>
                    @else
                        <x-ui.badge type="danger">Inactivo</x-ui.badge>
                    @endif
                </td>
                <td>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('¿Eliminar?')" class="btn btn-sm btn-outline-danger">Eliminar</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </x-ui.table>
    {{ $items->links() ?? '' }}
</x-ui.card>
@endsection
