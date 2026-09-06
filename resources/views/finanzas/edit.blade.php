<x-layouts.app page-title="Editar Operación">
<x-ui.page-header title="Editar Operación" />
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
                        <a href="{{ route('finanzas.pagos.show',$item) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        <a href="{{ route('finanzas.pagos.edit',$item) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3"><x-ui.empty-state message="Sin registros." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($items) && method_exists($items,'links'))
    <div class="px-3 py-2 border-top d-flex justify-content-between">
        <small class="text-muted">{{ $items->total() }} registros</small>
        {{ $items->links() }}
    </div>
    @endif
</x-ui.card>
</x-layouts.app>