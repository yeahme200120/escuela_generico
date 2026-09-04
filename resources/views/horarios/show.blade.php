@extends('components.layouts.app')
@section('content')
<x-ui.page-header :title="'Horario: ' . ($horario->nombre ?? '')" />
<x-ui.card>
    <dl class="row">
        <dt class="col-sm-3">Nombre</dt>
        <dd class="col-sm-9">{{ $horario->nombre }}</dd>
    </dl>
    <div class="d-flex justify-content-end">
        <a href="{{ route('horarios.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</x-ui.card>
@endsection
