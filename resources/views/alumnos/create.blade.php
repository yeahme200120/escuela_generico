<x-layouts.app page-title="Nuevo alumno">
<x-ui.page-header title="Nuevo alumno"
    :items="[['label'=>'Alumnos','url'=>route('alumnos.index')],['label'=>'Nuevo']]" />

<form method="POST" action="{{ route('alumnos.store') }}">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Datos personales">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror"
                           value="{{ old('nombres') }}" required>
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Apellido paterno <span class="text-danger">*</span></label>
                    <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror"
                           value="{{ old('apellido_paterno') }}" required>
                    @error('apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Apellido materno</label>
                    <input type="text" name="apellido_materno" class="form-control"
                           value="{{ old('apellido_materno') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">CURP</label>
                    <input type="text" name="curp" class="form-control @error('curp') is-invalid @enderror"
                           value="{{ old('curp') }}" maxlength="18" style="text-transform:uppercase">
                    @error('curp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control"
                           value="{{ old('fecha_nacimiento') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="">Seleccionar...</option>
                        <option value="M" {{ old('sexo')==='M'?'selected':'' }}>Masculino</option>
                        <option value="F" {{ old('sexo')==='F'?'selected':'' }}>Femenino</option>
                        <option value="otro" {{ old('sexo')==='otro'?'selected':'' }}>Otro</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Correo electrónico</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-medium">Dirección</label>
                    <textarea name="direccion" class="form-control" rows="2">{{ old('direccion') }}</textarea>
                </div>
            </div>
        </x-ui.card>
    </div>
    <div class="col-md-4">
        <x-ui.card title="Información escolar">
            <div class="mb-3">
                <label class="form-label fw-medium">Sede</label>
                <select name="sede_actual_id" class="form-select @error('sede_actual_id') is-invalid @enderror">
                    <option value="">Seleccionar...</option>
                    @foreach($sedes as $s)
                    <option value="{{ $s->id }}" {{ old('sede_actual_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                    @endforeach
                </select>
                @error('sede_actual_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Fecha de ingreso</label>
                <input type="date" name="fecha_ingreso" class="form-control"
                       value="{{ old('fecha_ingreso', now()->format('Y-m-d')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Matrícula</label>
                <input type="text" name="matricula" class="form-control" value="{{ old('matricula') }}"
                       placeholder="Se puede asignar después">
            </div>
        </x-ui.card>
    </div>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Registrar alumno</button>
    <a href="{{ route('alumnos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>
