<x-layouts.app page-title="Organizaciones">
<x-ui.page-header title="Organizaciones">
    <x-slot name="actions">
        @if(auth()->user()->esSuperadmin())
        <a href="{{ route('organizaciones.create') }}" class="btn btn-primary btn-sm">+ Nueva organización</a>
        @endif
    </x-slot>
</x-ui.page-header>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Nombre</th><th>RFC</th><th>Ciudad</th><th>Escuelas</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                @forelse($orgs as $o)
                <tr>
                    <td class="fw-medium">{{ $o->nombre }}</td>
                    <td style="font-size:.8rem">{{ $o->rfc ?? '—' }}</td>
                    <td style="font-size:.8rem">{{ $o->ciudad }}</td>
                    <td class="text-center">{{ $o->escuelas_count ?? 0 }}</td>
                    <td><x-ui.badge :type="$o->activa?'success':'secondary'">{{ $o->activa?'Activa':'Inactiva' }}</x-ui.badge></td>
                    <td class="text-end">
                        <a href="{{ route('organizaciones.show',$o) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @if(auth()->user()->esSuperadmin())
                        <a href="{{ route('organizaciones.edit',$o) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin organizaciones." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $orgs->links() }}</div>
</x-ui.card>
</x-layouts.app>
