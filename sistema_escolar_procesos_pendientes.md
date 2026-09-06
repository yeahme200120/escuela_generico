# Sistema Escolar — Procesos Pendientes
## Guía paso a paso con código para completar el sistema
### Actualizado: 2026-09-06

---

## Estado antes de continuar

```
php artisan route:list     ✅ ~290 rutas, 0 errores fatales
php artisan view:cache     ✅ Blade templates cached
npm run build              ✅ Bootstrap CSS 237KB + JS 137KB
BOM UTF-8                  ✅ 0 archivos afectados
@extends en vistas         ✅ 0 restantes
Clases faltantes           ✅ 0 referencias rotas
```

**Completado en esta sesión:**
- 52 controllers con lógica real (antes: ~17 con lógica)
- 36 Form Requests con validación y autorización
- 13 Policies con reglas reales
- 6 Middleware (3 nuevos: EnsureTwoFactor, EnsureOrganizacion, ScopeToOrganizacion)
- Services movidos a subcarpetas correctas (Academico/, Comunicacion/, Seguridad/)
- BOMs eliminados de todos los PHP

---

## P1 — Vistas críticas para uso del sistema

---

### PROCESO 1 — Vista alumnos/show completa §20-§22
**Archivo:** `resources/views/alumnos/show.blade.php`

```blade
<x-layouts.app page-title="Alumno: {{ $alumno->nombre_completo }}">
<x-ui.page-header title="{{ $alumno->nombre_completo }}"
    :items="[['label'=>'Alumnos','url'=>route('alumnos.index')],['label'=>$alumno->nombre_completo]]">
    <x-slot name="actions">
        @can('alumnos.editar')
        <a href="{{ route('alumnos.edit',$alumno) }}" class="btn btn-sm btn-outline-primary">Editar</a>
        @endcan
        @can('control_escolar.bajas')
        <a href="{{ route('bajas.create',['alumno_id'=>$alumno->id]) }}" class="btn btn-sm btn-outline-danger">Registrar baja</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<div class="row g-3">
    <div class="col-md-4">
        <x-ui.card title="Datos personales">
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-5 text-muted">Matrícula</dt><dd class="col-7">{{ $alumno->matricula ?? '—' }}</dd>
                <dt class="col-5 text-muted">CURP</dt><dd class="col-7">{{ $alumno->curp ?? '—' }}</dd>
                <dt class="col-5 text-muted">Nacimiento</dt><dd class="col-7">{{ $alumno->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</dd>
                <dt class="col-5 text-muted">Email</dt><dd class="col-7">{{ $alumno->email ?? '—' }}</dd>
                <dt class="col-5 text-muted">Estatus</dt>
                <dd class="col-7"><x-ui.badge :type="$alumno->estatus==='activo'?'success':'secondary'">{{ ucfirst($alumno->estatus) }}</x-ui.badge></dd>
                <dt class="col-5 text-muted">Riesgo</dt>
                <dd class="col-7"><x-ui.badge :type="match($alumno->estatus_riesgo){'riesgo_alto'=>'danger','riesgo_medio'=>'warning','observacion'=>'info',default=>'success'}">{{ str_replace('_',' ',ucfirst($alumno->estatus_riesgo)) }}</x-ui.badge></dd>
            </dl>
        </x-ui.card>
        <x-ui.card title="Tutores" class="mt-3">
            @forelse($alumno->tutores as $t)
            <div class="d-flex justify-content-between py-1 border-bottom" style="font-size:.875rem">
                <span>{{ $t->nombres }} {{ $t->apellido_paterno }}</span>
                <small class="text-muted">{{ $t->parentesco }}</small>
            </div>
            @empty <x-ui.empty-state message="Sin tutores." />
            @endforelse
        </x-ui.card>
    </div>
    <div class="col-md-8">
        <x-ui.card :flush="true">
            <ul class="nav nav-tabs px-3 pt-2" id="tabsAlumno" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-tray" type="button">Trayectoria</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bajas" type="button">Bajas</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cargos" type="button">Adeudos</button></li>
            </ul>
            <div class="tab-content p-3">
                <div class="tab-pane fade show active" id="tab-tray">
                    @forelse($alumno->trayectorias->sortByDesc('fecha_inicio') as $t)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:.875rem">
                        <div>
                            <strong>{{ $t->cicloEscolar?->nombre }}</strong>
                            <span class="text-muted ms-2">{{ $t->grado?->nombre }} {{ $t->grupo?->nombre }}</span>
                        </div>
                        <x-ui.badge type="secondary" small>{{ $t->estatus }}</x-ui.badge>
                    </div>
                    @empty <x-ui.empty-state message="Sin trayectoria registrada." />
                    @endforelse
                </div>
                <div class="tab-pane fade" id="tab-bajas">
                    @forelse($alumno->bajas as $b)
                    <div class="py-2 border-bottom" style="font-size:.875rem">
                        <x-ui.badge :type="$b->tipo==='temporal'?'warning':'danger'" small>{{ ucfirst($b->tipo) }}</x-ui.badge>
                        <span class="ms-2">{{ $b->fecha_solicitud?->format('d/m/Y') }}</span>
                        <p class="mb-0 text-muted">{{ $b->motivo }}</p>
                    </div>
                    @empty <x-ui.empty-state message="Sin bajas." />
                    @endforelse
                </div>
                <div class="tab-pane fade" id="tab-cargos">
                    @forelse($alumno->cargos->whereIn('estado',['pendiente','parcial','vencido']) as $c)
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.875rem">
                        <span>{{ $c->concepto?->nombre }}</span>
                        <span class="fw-semibold">${{ number_format($c->total,2) }}</span>
                        <x-ui.badge :type="$c->estado==='vencido'?'danger':'warning'" small>{{ ucfirst($c->estado) }}</x-ui.badge>
                    </div>
                    @empty <x-ui.empty-state message="Sin adeudos." />
                    @endforelse
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
```

**Controller update — AlumnoController::show():**
```php
public function show(int $id): View
{
    $this->authorize('alumnos.ver');
    $alumno = Alumno::where('organizacion_id', auth()->user()->organizacion_id)
        ->with(['trayectorias.cicloEscolar','trayectorias.grado','trayectorias.grupo','tutores','bajas','cargos.concepto'])
        ->findOrFail($id);
    return view('alumnos.show', compact('alumno'));
}
```

---

### PROCESO 2 — Vista calificaciones/index §41

**Archivo:** `resources/views/calificaciones/index.blade.php`

```blade
<x-layouts.app page-title="Calificaciones">
<x-ui.page-header title="Captura de calificaciones" />
<x-ui.filter-bar :action="route('calificaciones.index')">
    <x-slot name="fields">
        <div class="col-md-3">
            <label class="form-label form-label-sm">Grupo</label>
            <select name="grupo_id" class="form-select form-select-sm">
                <option value="">Seleccionar grupo...</option>
                @foreach($grupos as $g)
                <option value="{{ $g->id }}" {{ request('grupo_id')==$g->id?'selected':'' }}>{{ $g->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label form-label-sm">Periodo</label>
            <select name="periodo_id" class="form-select form-select-sm">
                <option value="">Seleccionar periodo...</option>
                @foreach($periodos as $p)
                <option value="{{ $p->id }}" {{ request('periodo_id')==$p->id?'selected':'' }}>{{ $p->nombre }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>
@if($alumnos->count())
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-sm mb-0">
            <thead>
                <tr>
                    <th>Alumno</th>
                    @foreach($materias as $m)<th class="text-center">{{ Str::limit($m->nombre,12) }}</th>@endforeach
                    <th class="text-center">Promedio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnos as $alumno)
                <tr>
                    <td style="font-size:.8rem" class="fw-medium">{{ $alumno->nombre_completo }}</td>
                    @foreach($materias as $m)
                    @php $cal = $calificaciones[$alumno->id][$m->id] ?? null; @endphp
                    <td class="text-center" style="font-size:.85rem">
                        @if($cal)
                            <span class="{{ $cal->resultado==='reprobado'?'text-danger fw-bold':'' }}">{{ $cal->calificacion ?? '—' }}</span>
                        @else
                            @can('calificaciones.registrar')
                            <a href="{{ route('calificaciones.create',['alumno_id'=>$alumno->id,'materia_id'=>$m->id,'periodo_id'=>request('periodo_id')]) }}" class="text-muted btn btn-link btn-sm p-0">+</a>
                            @else —
                            @endcan
                        @endif
                    </td>
                    @endforeach
                    <td class="text-center fw-semibold">{{ $promedios[$alumno->id] ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-ui.card>
@else
<x-ui.card><x-ui.empty-state message="Selecciona grupo y periodo para ver calificaciones." /></x-ui.card>
@endif
</x-layouts.app>
```

---

### PROCESO 3 — Vista asistencias/index (pase de lista) §39

**Archivo:** `resources/views/asistencias/index.blade.php`

```blade
<x-layouts.app page-title="Asistencias">
<x-ui.page-header title="Pase de lista" />
<x-ui.filter-bar :action="route('asistencias.index')">
    <x-slot name="fields">
        <div class="col-md-3">
            <label class="form-label form-label-sm">Grupo</label>
            <select name="grupo_id" class="form-select form-select-sm">
                <option value="">Seleccionar grupo...</option>
                @foreach($grupos as $g)
                <option value="{{ $g->id }}" {{ request('grupo_id')==$g->id?'selected':'' }}>{{ $g->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label form-label-sm">Fecha</label>
            <input type="date" name="fecha" class="form-control form-control-sm" value="{{ request('fecha', now()->format('Y-m-d')) }}">
        </div>
    </x-slot>
</x-ui.filter-bar>
@if(request('grupo_id') && $alumnos->count())
<x-ui.card title="Lista — {{ \Carbon\Carbon::parse(request('fecha'))->format('d/m/Y') }}">
<form method="POST" action="{{ route('asistencias.store') }}">
    @csrf
    <input type="hidden" name="grupo_id" value="{{ request('grupo_id') }}">
    <input type="hidden" name="fecha" value="{{ request('fecha') }}">
    <input type="hidden" name="ciclo_id" value="{{ $cicloActual?->id }}">
    <div class="table-responsive">
        <table class="table table-se mb-0">
            <thead><tr><th>#</th><th>Alumno</th><th>Estado</th><th>Observación</th></tr></thead>
            <tbody>
                @foreach($alumnos as $i => $alumno)
                @php $prev = $asistenciasExistentes[$alumno->id] ?? null; @endphp
                <tr class="{{ ($prev && $prev['estado']==='falta') ? 'table-danger bg-opacity-25' : '' }}">
                    <td>{{ $i+1 }}</td>
                    <td class="fw-medium" style="font-size:.875rem">{{ $alumno->nombre_completo }}</td>
                    <td>
                        <input type="hidden" name="lista[{{ $i }}][alumno_id]" value="{{ $alumno->id }}">
                        <select name="lista[{{ $i }}][estado]" class="form-select form-select-sm" style="width:140px">
                            @foreach(['presente'=>'Presente','falta'=>'Falta','retardo'=>'Retardo','justificada'=>'Justificada'] as $v=>$l)
                            <option value="{{ $v }}" {{ ($prev['estado'] ?? 'presente') === $v ? 'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="lista[{{ $i }}][observacion]" class="form-control form-control-sm" value="{{ $prev['observacion'] ?? '' }}" placeholder="Opcional"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="d-flex gap-2 p-3">
        <button type="submit" class="btn btn-primary btn-sm">Guardar pase</button>
        <span class="text-muted align-self-center" style="font-size:.8rem">{{ $alumnos->count() }} alumnos</span>
    </div>
</form>
</x-ui.card>
@elseif(request('grupo_id'))
<x-ui.card><x-ui.empty-state message="No hay alumnos activos en este grupo." /></x-ui.card>
@endif
</x-layouts.app>
```

---

### PROCESO 4 — Vista users/index (CRUD usuarios) §14

**Archivo:** `resources/views/users/index.blade.php`

```blade
<x-layouts.app page-title="Usuarios">
<x-ui.page-header title="Usuarios del sistema">
    <x-slot name="actions">
        @can('usuarios.crear')
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">+ Nuevo usuario</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<x-ui.filter-bar :action="route('users.index')">
    <x-slot name="fields">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Nombre, email, usuario..." value="{{ request('q') }}">
        </div>
        <div class="col-md-2">
            <select name="activo" class="form-select form-select-sm">
                <option value="">Estado</option>
                <option value="1" {{ request('activo')==='1'?'selected':'' }}>Activos</option>
                <option value="0" {{ request('activo')==='0'?'selected':'' }}>Inactivos</option>
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Usuario</th><th>Email</th><th>Roles</th><th>Sede</th><th>Estado</th><th>Último acceso</th><th></th></tr></thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <x-ui.avatar :name="$u->nombre_completo" size="sm" />
                            <div>
                                <div class="fw-medium" style="font-size:.875rem">{{ $u->nombre_completo }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ $u->username ?? $u->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.8rem">{{ $u->email }}</td>
                    <td>@foreach($u->roles->take(2) as $r)<x-ui.badge type="secondary" :small="true">{{ $r->nombre }}</x-ui.badge> @endforeach</td>
                    <td style="font-size:.8rem">{{ $u->sedePrincipal()?->nombre ?? '—' }}</td>
                    <td><x-ui.badge :type="$u->activo?'success':'secondary'">{{ $u->activo?'Activo':'Inactivo' }}</x-ui.badge></td>
                    <td style="font-size:.78rem">{{ $u->ultimo_acceso_at?->diffForHumans() ?? 'Nunca' }}</td>
                    <td class="text-end">
                        <a href="{{ route('users.show',$u) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @can('usuarios.editar')
                        <a href="{{ route('users.edit',$u) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><x-ui.empty-state message="Sin usuarios registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $users->links() }}</div>
</x-ui.card>
</x-layouts.app>
```

---

### PROCESO 5 — Vistas finanzas completas §47-§52

**`resources/views/finanzas/cargos/index.blade.php`:**
```blade
<x-layouts.app page-title="Cargos">
<x-ui.page-header title="Cargos de alumnos">
    <x-slot name="actions">
        @can('pagos.registrar')
        <a href="{{ route('finanzas.cargos.create') }}" class="btn btn-sm btn-primary">+ Nuevo cargo</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<x-ui.filter-bar :action="route('finanzas.cargos.index')">
    <x-slot name="fields">
        <div class="col-md-4"><input type="text" name="q" class="form-control form-control-sm" placeholder="Alumno, matrícula..." value="{{ request('q') }}"></div>
        <div class="col-md-2">
            <select name="estado" class="form-select form-select-sm">
                <option value="">Estado</option>
                @foreach(['pendiente','parcial','pagado','cancelado','vencido'] as $e)
                <option value="{{ $e }}" {{ request('estado')===$e?'selected':'' }}>{{ ucfirst($e) }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Alumno</th><th>Concepto</th><th>Total</th><th>Vencimiento</th><th>Estado</th><th></th></tr></thead>
            <tbody>
                @forelse($cargos as $c)
                <tr>
                    <td style="font-size:.875rem">{{ $c->alumno?->nombre_completo }}</td>
                    <td style="font-size:.875rem">{{ $c->concepto?->nombre }}</td>
                    <td class="fw-semibold">${{ number_format($c->total,2) }}</td>
                    <td style="font-size:.8rem">{{ $c->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                    <td><x-ui.badge :type="match($c->estado){'pendiente'=>'warning','pagado'=>'success','cancelado'=>'secondary','vencido'=>'danger',default=>'info'}">{{ ucfirst($c->estado) }}</x-ui.badge></td>
                    <td class="text-end">
                        <a href="{{ route('alumnos.show',$c->alumno_id) }}" class="btn btn-sm btn-outline-secondary">Ver alumno</a>
                        @if(in_array($c->estado,['pendiente','parcial','vencido']))
                        <a href="{{ route('finanzas.pagos.create',['alumno_id'=>$c->alumno_id]) }}" class="btn btn-sm btn-outline-success ms-1">Cobrar</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin cargos registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $cargos->links() }}</div>
</x-ui.card>
</x-layouts.app>
```

---

### PROCESO 6 — Dashboard enriquecido por rol §73-§75

**Actualizar `routes/web.php` closure de dashboard:**
```php
Route::get('/dashboard', function () {
    $user        = auth()->user();
    $sedeId      = $user->sedePrincipal()?->id;
    $ciclo       = \App\Models\CicloEscolar::where('es_actual', true)->first();
    $indicadores = [];
    $riesgo      = [];

    if ($sedeId && $ciclo) {
        try {
            $indicadores = app(\App\Services\Academico\IndicadoresService::class)
                ->calcularIndicadoresSede($sedeId, $ciclo->id);
            $riesgo = app(\App\Services\Academico\RiesgoAcademicoService::class)
                ->calcularMasivo($sedeId, $ciclo->id);
        } catch (\Throwable $e) {
            // No bloquear el dashboard si falla el cálculo
        }
    }
    return view('dashboard', compact('indicadores', 'riesgo', 'ciclo'));
})->name('dashboard');
```

**Actualizar `resources/views/dashboard.blade.php` stat cards:**
```blade
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <x-ui.stat-card label="Alumnos activos"
            :value="$indicadores['inscritos'] ?? '—'" color="primary" :link="route('alumnos.index')"/>
    </div>
    <div class="col-6 col-xl-3">
        <x-ui.stat-card label="% Aprobación"
            :value="($indicadores['pct_aprobacion'] ?? '—').'%'" color="success"/>
    </div>
    <div class="col-6 col-xl-3">
        <x-ui.stat-card label="En riesgo"
            :value="($riesgo['riesgo_alto'] ?? 0) + ($riesgo['riesgo_medio'] ?? 0)"
            color="danger" :link="route('alumnos.index')"/>
    </div>
    <div class="col-6 col-xl-3">
        <x-ui.stat-card label="% Deserción"
            :value="($indicadores['pct_desercion'] ?? '—').'%'" color="warning"/>
    </div>
</div>
```

---

## P2 — Módulos académicos operativos

### PROCESO 7 — Vistas docentes CRUD §34-§35
Archivos: `resources/views/docentes/index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`

**Patrón index (aplicar a todos):**
```blade
<x-layouts.app page-title="Docentes">
<x-ui.page-header title="Docentes">
    <x-slot name="actions">
        @can('docentes.crear')
        <a href="{{ route('docentes.create') }}" class="btn btn-primary btn-sm">+ Nuevo docente</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<x-ui.filter-bar :action="route('docentes.index')">
    <x-slot name="fields">
        <div class="col-md-4"><input type="text" name="q" class="form-control form-control-sm" placeholder="Nombre, email..." value="{{ request('q') }}"></div>
        <div class="col-md-2">
            <select name="estatus" class="form-select form-select-sm">
                <option value="">Estatus</option>
                <option value="activo" {{ request('estatus')==='activo'?'selected':'' }}>Activo</option>
                <option value="inactivo" {{ request('estatus')==='inactivo'?'selected':'' }}>Inactivo</option>
            </select>
        </div>
    </x-slot>
</x-ui.filter-bar>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se table-hover mb-0">
            <thead><tr><th>Docente</th><th>Especialidad</th><th>Contrato</th><th>Estatus</th><th></th></tr></thead>
            <tbody>
                @forelse($docentes as $d)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <x-ui.avatar :name="$d->user?->nombre_completo" size="sm"/>
                            <div>
                                <div class="fw-medium" style="font-size:.875rem">{{ $d->user?->nombre_completo }}</div>
                                <div class="text-muted" style="font-size:.75rem">{{ $d->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.875rem">{{ $d->especialidad ?? '—' }}</td>
                    <td><x-ui.badge type="info" small>{{ ucfirst($d->tipo_contrato) }}</x-ui.badge></td>
                    <td><x-ui.badge :type="$d->estatus==='activo'?'success':'secondary'">{{ ucfirst($d->estatus) }}</x-ui.badge></td>
                    <td class="text-end">
                        <a href="{{ route('docentes.show',$d) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @can('docentes.editar')
                        <a href="{{ route('docentes.edit',$d) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="5"><x-ui.empty-state message="Sin docentes registrados." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $docentes->links() }}</div>
</x-ui.card>
</x-layouts.app>
```

---

### PROCESO 8 — Vistas roles/index y roles/create §17

**`resources/views/roles/index.blade.php`:**
```blade
<x-layouts.app page-title="Roles">
<x-ui.page-header title="Roles del sistema">
    <x-slot name="actions">
        @can('roles.asignar')
        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm">+ Nuevo rol</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<x-ui.card :flush="true">
    <div class="table-responsive">
        <table class="table table-se mb-0">
            <thead><tr><th>Nombre</th><th>Slug</th><th>Nivel</th><th>Permisos</th><th>Sistema</th><th></th></tr></thead>
            <tbody>
                @forelse($roles as $rol)
                <tr>
                    <td class="fw-medium">{{ $rol->nombre }}</td>
                    <td><code style="font-size:.8rem">{{ $rol->slug }}</code></td>
                    <td><x-ui.badge type="secondary" small>{{ $rol->nivel }}</x-ui.badge></td>
                    <td><x-ui.badge type="info" small>{{ $rol->permissions_count }}</x-ui.badge></td>
                    <td>{{ $rol->es_sistema ? '🔒' : '' }}</td>
                    <td class="text-end">
                        <a href="{{ route('roles.show',$rol) }}" class="btn btn-sm btn-outline-secondary">Ver</a>
                        @if(!$rol->es_sistema)
                        @can('roles.asignar')
                        <a href="{{ route('roles.edit',$rol) }}" class="btn btn-sm btn-outline-primary ms-1">Editar</a>
                        @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6"><x-ui.empty-state message="Sin roles." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-3 py-2 border-top">{{ $roles->links() }}</div>
</x-ui.card>
</x-layouts.app>
```

---

## P3 — Infraestructura Python §3, §76-§78, §109

### PROCESO 9 — Crear directorio python/ base

**Estructura completa:**
```
python/
├── app/
│   ├── main.py              FastAPI entry point
│   ├── workers/
│   │   ├── estadisticas.py
│   │   ├── riesgo.py
│   │   ├── importaciones.py
│   │   ├── horarios.py
│   │   └── reportes.py
│   └── schemas.py
├── requirements.txt
├── .env.example
└── tests/
    └── test_workers.py
```

**`python/requirements.txt`:**
```
fastapi==0.115.0
uvicorn==0.30.6
pandas==2.2.3
openpyxl==3.1.5
python-multipart==0.0.12
httpx==0.27.2
python-dotenv==1.0.1
```

**`python/app/main.py`:**
```python
from fastapi import FastAPI, Request, HTTPException
import os

app = FastAPI(title="Sistema Escolar — Python Worker", version="1.0.0")
SECRET = os.getenv("PYTHON_SERVICE_SECRET", "")

@app.middleware("http")
async def auth_middleware(request: Request, call_next):
    if request.url.path not in ["/", "/health", "/docs", "/openapi.json"]:
        if request.headers.get("X-Python-Secret") != SECRET:
            raise HTTPException(status_code=401, detail="Unauthorized")
    return await call_next(request)

@app.get("/")
@app.get("/health")
async def health(): return {"status": "ok", "service": "Sistema Escolar Python Worker"}

@app.post("/jobs/{tipo}")
async def ejecutar_job(tipo: str, data: dict):
    job_id = data.get("job_id", "unknown")
    handlers = {
        "estadisticas": calcular_estadisticas,
        "riesgo":       calcular_riesgo,
        "importacion":  procesar_importacion,
        "reporte":      generar_reporte,
        "horario":      generar_horario,
    }
    handler = handlers.get(tipo)
    if not handler:
        raise HTTPException(status_code=404, detail=f"Tipo '{tipo}' no soportado")
    result = await handler(data)
    return {"job_id": job_id, "status": "completed", "results": result}

async def calcular_estadisticas(data: dict) -> dict:
    # TODO: implementar con pandas
    return {"approval_rate": 0, "failure_rate": 0, "dropout_rate": 0}

async def calcular_riesgo(data: dict) -> dict:
    return {"normal": 0, "observacion": 0, "riesgo_medio": 0, "riesgo_alto": 0}

async def procesar_importacion(data: dict) -> dict:
    return {"procesados": 0, "errores": 0, "advertencias": []}

async def generar_reporte(data: dict) -> dict:
    return {"archivo": "", "registros": 0}

async def generar_horario(data: dict) -> dict:
    return {"horario": [], "conflictos": []}
```

**Agregar al `.env`:**
```
PYTHON_SERVICE_URL=http://localhost:8001
PYTHON_SERVICE_SECRET=cambiar-en-produccion
```

**Ejecutar:**
```bash
cd python
pip install -r requirements.txt
uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload
```

---

## P3 — API REST, 2FA y Tests

### PROCESO 10 — Completar routes/api.php §89
```php
use App\Http\Controllers\Alumnos\AlumnoController;
use App\Models\PythonJob;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $r) => $r->user());

    // Alumnos
    Route::get('/alumnos',      [AlumnoController::class, 'index']);
    Route::get('/alumnos/{id}', [AlumnoController::class, 'show']);

    // Calificaciones
    Route::get('/calificaciones',  [\App\Http\Controllers\CalificacionController::class, 'index']);
    Route::post('/calificaciones', [\App\Http\Controllers\CalificacionController::class, 'store']);

    // Asistencias
    Route::post('/asistencias', [\App\Http\Controllers\AsistenciaController::class, 'store']);

    // Python jobs — polling de estado §78
    Route::get('/jobs/{jobId}', function (string $jobId) {
        $job = PythonJob::where('job_id', $jobId)->firstOrFail();
        return response()->json([
            'job_id'    => $job->job_id,
            'estado'    => $job->estado,
            'progreso'  => $job->progreso,
            'resultado' => $job->resultado,
        ]);
    });
});
```

### PROCESO 11 — Tests básicos §97-§98
**Ejecutar:** `php artisan test`

**`tests/Feature/Auth/LoginTest.php`:**
```php
<?php
namespace Tests\Feature\Auth;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_correcto(): void
    {
        $user = User::factory()->create(['password'=>bcrypt('password'),'activo'=>true]);
        $this->post('/login', ['email'=>$user->email,'password'=>'password'])
             ->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_login_incorrecto(): void
    {
        $user = User::factory()->create(['activo'=>true]);
        $this->post('/login', ['email'=>$user->email,'password'=>'wrong'])
             ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_usuario_inactivo_no_puede_entrar(): void
    {
        $user = User::factory()->create(['password'=>bcrypt('password'),'activo'=>false]);
        $this->post('/login', ['email'=>$user->email,'password'=>'password'])
             ->assertRedirect('/login');
    }
}
```

---

## Resumen de prioridades

| # | Proceso | Archivo principal | Prioridad |
|---|---------|------------------|-----------|
| 1 | Vista alumnos/show | `alumnos/show.blade.php` | 🔴 P1 |
| 2 | Vista calificaciones | `calificaciones/index.blade.php` | 🔴 P1 |
| 3 | Vista asistencias pase lista | `asistencias/index.blade.php` | 🔴 P1 |
| 4 | Vista users CRUD | `users/index.blade.php` | 🔴 P1 |
| 5 | Vista finanzas cargos/pagos/caja | `finanzas/*/index.blade.php` | 🔴 P1 |
| 6 | Dashboard con indicadores reales | `dashboard.blade.php` | 🟡 P2 |
| 7 | Vistas docentes CRUD | `docentes/*.blade.php` | 🟡 P2 |
| 8 | Vistas roles CRUD | `roles/*.blade.php` | 🟡 P2 |
| 9 | Infraestructura Python | `python/` directorio | 🟡 P2 |
| 10 | API REST completa | `routes/api.php` | 🟠 P3 |
| 11 | Tests básicos | `tests/Feature/` | 🟠 P3 |
| 12 | Vistas 2FA y password reset | `two-factor/`, `auth/` | 🟠 P3 |
