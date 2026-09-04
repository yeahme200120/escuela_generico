@extends('components.layouts.app')
@section('content')
<x-ui.page-header title="Finanzas" />
<x-ui.card>
    <p>Panel financiero: cargos, pagos y caja.</p>
    <div class="d-flex gap-2">
        <a href="{{ route('finanzas.cargos.index') }}" class="btn btn-outline-primary">Cargos</a>
        <a href="{{ route('finanzas.pagos.index') }}" class="btn btn-outline-primary">Pagos</a>
        <a href="{{ route('finanzas.caja.index') }}" class="btn btn-outline-primary">Caja</a>
    </div>
</x-ui.card>
@endsection
