@extends('components.layouts.app')
@section('content')
<x-ui.page-header title="Horarios" />
<x-ui.card>
    <x-ui.table>
        @slot('thead')
            <tr><th>#</th><th>Grupo</th><th>Acciones</th></tr>
        @endslot
        @foreach($items ?? [] as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->nombre ?? $item->grupo_nombre }}</td>
                <td>
                    <a href="#" class="btn btn-sm btn-outline-primary">Ver</a>
                    <a href="#" class="btn btn-sm btn-outline-secondary">Editar</a>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
</x-ui.card>
@endsection
