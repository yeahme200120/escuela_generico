@extends('components.layouts.app')
@section('content')
<x-ui.page-header title="Calificaciones" subtitle="Gestión de calificaciones académicas">
    <a href="{{ route('calificaciones.create') }}" class="btn btn-primary">+ Registrar Calificación</a>
</x-ui.page-header>
<x-ui.card>
    <x-ui.table>
        @slot('thead')
            <tr>
                <th>Alumno</th>
                <th>Materia</th>
                <th>Período</th>
                <th>Calificación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        @endslot
        @foreach($items ?? [] as $cal)
            <tr>
                <td>{{ $cal->alumno->nombre ?? '—' }}</td>
                <td>{{ $cal->materia->nombre ?? '—' }}</td>
                <td>{{ $cal->periodo->nombre ?? '—' }}</td>
                <td>
                    <strong>{{ $cal->calificacion }}</strong>/100
                </td>
                <td>
                    @if($cal->calificacion >= 70)
                        <x-ui.badge type="success">Aprobado</x-ui.badge>
                    @else
                        <x-ui.badge type="danger">Reprobado</x-ui.badge>
                    @endif
                </td>
                <td>
                    <a href="{{ route('calificaciones.edit', $cal) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                </td>
            </tr>
        @endforeach
    </x-ui.table>
    {{ $items->links() ?? '' }}
</x-ui.card>
@endsection
