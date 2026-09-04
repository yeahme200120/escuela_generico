<x-layouts.app page-title="Editar alumno">
<x-ui.page-header title="Editar alumno" subtitle="Módulo Alumnos" />
<form action="{{ route('alumnos.update', $alumno) }}" method="POST">
    @csrf
    @method('PUT')
    <x-ui.card>
        <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input name="nombre" value="{{ old('nombre', $alumno->nombre) }}" class="form-control" required />
        </div>
        <div class="d-flex justify-content-end">
            <a href="{{ route('alumnos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
            <button class="btn btn-primary">Guardar</button>
        </div>
    </x-ui.card>
</form>
</x-layouts.app>
