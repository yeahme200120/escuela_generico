<x-layouts.app page-title="Editar alumno">
<x-ui.page-header title="Editar: {{ $alumno->nombre_completo }}"
    :items="[['label'=>'Alumnos','url'=>route('alumnos.index')],['label'=>$alumno->nombre_completo,'url'=>route('alumnos.show',$alumno)],['label'=>'Editar']]" />

<form method="POST" action="{{ route('alumnos.update',$alumno) }}">
@csrf @method('PUT')
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Datos personales">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-medium">Nombres <span class="text-danger">*</span></label>
                    <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror"
                           value="{{ old('nombres',$alumno->nombres) }}" required>
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Apellido paterno <span class="text-danger">*</span></label>
                    <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror"
                           value="{{ old('apellido_paterno',$alumno->apellido_paterno) }}" required>
                    @error('apellido_paterno')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Apellido materno</label>
                    <input type="text" name="apellido_materno" class="form-control"
                           value="{{ old('apellido_materno',$alumno->apellido_materno) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">CURP</label>
                    <input type="text" name="curp" class="form-control @error('curp') is-invalid @enderror"
                           value="{{ old('curp',$alumno->curp) }}" maxlength="18" style="text-transform:uppercase">
                    @error('curp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Fecha de nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control"
                           value="{{ old('fecha_nacimiento',$alumno->fecha_nacimiento?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Sexo</label>
                    <select name="sexo" class="form-select">
                        <option value="">Seleccionar...</option>
                        @foreach(['M'=>'Masculino','F'=>'Femenino','otro'=>'Otro'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('sexo',$alumno->sexo)===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Correo electrónico</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email',$alumno->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="{{ old('telefono',$alumno->telefono) }}">
                </div>
            </div>
        </x-ui.card>
    </div>
    <div class="col-md-4">
        <x-ui.card title="Estado académico">
            <div class="mb-3">
                <label class="form-label fw-medium">Matrícula</label>
                <input type="text" name="matricula" class="form-control"
                       value="{{ old('matricula',$alumno->matricula) }}">
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Situación inscripción</label>
                <select name="situacion_inscripcion" class="form-select">
                    @foreach(['inscrito','reinscrito','pendiente','no_reinscrito','cancelada'] as $s)
                    <option value="{{ $s }}" {{ old('situacion_inscripcion',$alumno->situacion_inscripcion)===$s?'selected':'' }}>
                        {{ ucfirst(str_replace('_',' ',$s)) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-0">
                <label class="form-label fw-medium">Situación académica</label>
                <select name="situacion_academica" class="form-select">
                    @foreach(['regular','irregular','reprobado','en_regularizacion','condicionado'] as $s)
                    <option value="{{ $s }}" {{ old('situacion_academica',$alumno->situacion_academica)===$s?'selected':'' }}>
                        {{ ucfirst(str_replace('_',' ',$s)) }}
                    </option>
                    @endforeach
                </select>
            </div>
        </x-ui.card>
    </div>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Guardar cambios</button>
    <a href="{{ route('alumnos.show',$alumno) }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>
