<x-layouts.app page-title="Niveles Educativos">
<x-ui.page-header title="Niveles Educativos">
    <x-slot name="actions">
        <a href="{{ route('niveles.create') }}" class="btn btn-primary btn-sm">+ Nuevo nivel</a>
    </x-slot>
</x-ui.page-header>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Nombre</th><th>Escuela</th><th>Duración</th><th>Orden</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                @forelse($niveles as $n)
                <tr>
                    <td class="fw-medium">{{ $n->nombre }}</td>
                    <td>{{ $n->escuela->nombre }}</td>
                    <td>{{ $n->duracion_anos }} año(s)</td>
                    <td>{{ $n->orden }}</td>
                    <td><x-ui.badge :type="$n->activo?'success':'secondary'">{{ $n->activo?'Activo':'Inactivo' }}</x-ui.badge></td>
                    <td class="text-end">
                        <a href="{{ route('niveles.edit', $n) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin niveles registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $niveles->links() }}</div>
</x-ui.card>
</x-layouts.app>
