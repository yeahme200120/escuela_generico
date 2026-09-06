<x-layouts.app page-title="Nuevo Horarios">
<x-ui.page-header title="Nuevo Horarios"
    :items="[['label'=>'Horarios','url'=>route('horarios.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('horarios.store') }}">
@csrf
<x-ui.card title="Datos">
    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Dia Semana</label>
                    <input type="text" name="dia_semana" class="form-control @error('dia_semana') is-invalid @enderror"
                           value="{{ old('dia_semana') }}">
                    @error('dia_semana')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Hora Inicio</label>
                    <input type="text" name="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror"
                           value="{{ old('hora_inicio') }}">
                    @error('hora_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Hora Fin</label>
                    <input type="text" name="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror"
                           value="{{ old('hora_fin') }}">
                    @error('hora_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="{{ route('horarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>