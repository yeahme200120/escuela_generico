<x-layouts.app page-title="Lista de alumnos">
<x-ui.page-header title="Lista de alumnos" subtitle="Módulo Alumnos" />
<x-ui.card>
    <x-ui.table>
        @slot('thead')
            <tr><th>#</th><th>Nombre</th><th>Acciones</th></tr>
        @endslot
        @foreach($items ?? [] as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nombre }}</td>
                <td>
                    <a href="{{ route('alumnos.show', $item) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                    <a href="{{ route('alumnos.edit', $item) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
</x-ui.card>
</x-layouts.app>
