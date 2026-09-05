<x-layouts.app page-title="Editar Ciclo">
<x-ui.page-header title="Editar Ciclo: {{ $ciclo->nombre }}" :items="[['label'=>'Ciclos','url'=>route('ciclos.index')],['label'=>$ciclo->nombre,'url'=>route('ciclos.show',$ciclo)],['label'=>'Editar']]" />
<x-ui.card title="Datos del ciclo">
<form method="POST" action="{{ route('ciclos.update', $ciclo) }}">
    @csrf @method('PUT')
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-medium">Nombre</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                value="{{ old('nombre', $ciclo->nombre) }}" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Fecha inicio</label>
            <input type="date" name="fecha_inicio" class="form-control"
                value="{{ old('fecha_inicio', $ciclo->fecha_inicio->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-medium">Fecha fin</label>
            <input type="date" name="fecha_fin" class="form-control"
                value="{{ old('fecha_fin', $ciclo->fecha_fin->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-medium">Estado</label>
            <select name="estatus" class="form-select">
                @foreach(['configuracion'=>'Configuración','activo'=>'Activo','cerrado'=>'Cerrado','archivado'=>'Archivado'] as $v=>$l)
                <option value="{{ $v }}" {{ (old('estatus',$ciclo->estatus)==$v)?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check mb-2">
                <input type="checkbox" name="es_actual" value="1" class="form-check-input" id="es_actual"
                    {{ old('es_actual', $ciclo->es_actual) ? 'checked' : '' }}>
                <label class="form-check-label" for="es_actual">Ciclo actual</label>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('ciclos.show', $ciclo) }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
</form>
</x-ui.card>
</x-layouts.app>
