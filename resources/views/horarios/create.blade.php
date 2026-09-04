@extends('components.layouts.app')
@section('content')
<x-ui.page-header title="Crear horario" />
<form action="{{ route('horarios.store') }}" method="POST">
    @csrf
    <x-ui.card>
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input name="nombre" class="form-control" required />
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('horarios.index') }}" class="btn btn-secondary me-2">Cancelar</a>
            <button class="btn btn-primary">Guardar</button>
        </div>
    </x-ui.card>
</form>
@endsection
