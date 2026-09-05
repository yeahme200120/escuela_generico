@props([
    'headers'   => [],      // array de strings
    'striped'   => false,
    'hover'     => true,
    'bordered'  => false,
    'sm'        => false,
    'empty'     => 'No hay registros que mostrar.',
    'responsive' => true,
])
@php
$tableClass = collect([
    'table table-se',
    $striped  ? 'table-striped'  : '',
    $hover    ? 'table-hover'    : '',
    $bordered ? 'table-bordered' : '',
    $sm       ? 'table-sm'       : '',
])->filter()->implode(' ');
@endphp

<div class="{{ $responsive ? 'table-responsive' : '' }}">
    <table {{ $attributes->merge(['class' => $tableClass]) }}>
        @if(count($headers))
        <thead>
            <tr>
                @foreach($headers as $h)
                <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
        @isset($tfoot)
        <tfoot>{{ $tfoot }}</tfoot>
        @endisset
    </table>
</div>

{{-- Estado vacío si el slot está vacío --}}
@php $slotContent = trim((string) $slot); @endphp
@if(!$slotContent)
<x-ui.empty-state message="{{ $empty }}" />
@endif
