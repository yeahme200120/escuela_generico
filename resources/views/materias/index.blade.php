@extends('components.layouts.app')
@section('content')
<x-ui.page-header title="Materias" subtitle="Gestión de materias académicas">
    <a href="{{ route('materias.create') }}" class="btn btn-primary">+ Nueva Materia</a>
</x-ui.page-header>
<x-ui.card>
    <x-ui.table>
        @slot('thead')
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Código</th>
                <th>Horas</th>
                <th>Acciones</th>
            </tr>
        @endslot
        @foreach($items ?? [] as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td><strong>{{ $item->nombre }}</strong></td>
                <td><code>{{ $item->codigo }}</code></td>
                <td>{{ $item->horas_semanales ?? '—' }} h</td>
                <td>
                    <a href="{{ route('materias.show', $item) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                    <a href="{{ route('materias.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                    <form action="{{ route('materias.destroy', $item) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('¿Eliminar?')" class="btn btn-sm btn-outline-danger">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
    {{ $items->links() ?? '' }}
</x-ui.card>
@endsection
