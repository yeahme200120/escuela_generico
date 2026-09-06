<x-layouts.app page-title="Niveles">
<x-ui.page-header title="Niveles">
    <x-slot name="actions">
        <a href="{{ route('niveles.create') }}" class="btn btn-primary btn-sm">+ Nuevo</a>
    </x-slot>
</x-ui.page-header>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Descripción</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                @forelse($items ?? [] as $item)
                <tr>
                    <td class="fw-medium" style="font-size:.875rem">{{ $item->nombre ?? $item->titulo ?? $item->id }}</td>
                    <td><x-ui.badge type="secondary" small>{{ $item->estatus ?? $item->estado ?? '—' }}</x-ui.badge></td>
                    <td class="text-end">
                        <a href="{{ route('niveles.show',$item) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        <a href="{{ route('niveles.edit',$item) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3"><x-ui.empty-state message="Sin registros." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($items) && method_exists($items,'links'))
    <div class="px-3 py-2 border-top">{{ $items->links() }}</div>
    @endif
</x-ui.card>
</x-layouts.app>