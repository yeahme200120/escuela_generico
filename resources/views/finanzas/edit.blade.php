@extends('components.layouts.app')
@section('content')
<x-ui.page-header title="Editar cargo" />
<form action="{{ route('finanzas.cargos.update', $cargo) }}" method="POST">
    @csrf
    @method('PUT')
    <x-ui.card>
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <input name="descripcion" value="{{ old('descripcion', $cargo->descripcion) }}" class="form-control" required />
        </div>
        <div class="mb-3">
            <label class="form-label">Monto</label>
            <input name="monto" type="number" step="0.01" value="{{ old('monto', $cargo->monto) }}" class="form-control" required />
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('finanzas.cargos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
            <button class="btn btn-primary">Guardar</button>
        </div>
    </x-ui.card>
</form>
@endsection
