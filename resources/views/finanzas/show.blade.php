@extends('components.layouts.app')
@section('content')
<x-ui.page-header :title="'Cargo: ' . ($cargo->descripcion ?? '')" />
<x-ui.card>
    <dl class="row">
        <dt class="col-sm-3">Descripción</dt>
        <dd class="col-sm-9">{{ $cargo->descripcion }}</dd>
        <dt class="col-sm-3">Monto</dt>
        <dd class="col-sm-9">{{ number_format($cargo->monto, 2) }}</dd>
    </dl>
    <div class="d-flex justify-content-end">
        <a href="{{ route('finanzas.cargos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</x-ui.card>
@endsection
