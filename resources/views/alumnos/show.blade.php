<x-layouts.app page-title="Ficha del alumno">
<x-ui.page-header title="Ficha del alumno" subtitle="Módulo Alumnos" />
<x-ui.card>
    <dl class="row">
        <dt class="col-sm-3">Nombre</dt>
        <dd class="col-sm-9">{{ $alumno->nombre }}</dd>
    </dl>
    <div class="d-flex justify-content-end">
        <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Volver</a>
    </div>
</x-ui.card>
</x-layouts.app>
