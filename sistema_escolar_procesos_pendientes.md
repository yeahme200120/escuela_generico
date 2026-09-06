# Sistema Escolar — Procesos Pendientes
## Lo que falta y cómo implementarlo
### Actualizado: 2026-09-06

---

## Estado actual verificado

```
php artisan route:list     ✅ 307 rutas · 0 errores
php artisan view:cache     ✅ OK
npm run build              ✅ 237KB CSS · 137KB JS
BOMs en PHP                ✅ 0
@extends en vistas         ✅ 0
Clases PHP faltantes       ✅ 0
Python workers             ✅ 5 workers en python/app/workers/
Vistas completas           ✅ 127
Vistas stub                ⚠️  77 (funcionales pero sin lógica específica)
```

---

## P1 — Vistas stub prioritarias con lógica real

### 1. finanzas/pagos/create — Formulario de cobro

```blade
{{-- resources/views/finanzas/pagos/create.blade.php --}}
<x-layouts.app page-title="Registrar Pago">
<x-ui.page-header title="Registrar Pago"
    :items="[['label'=>'Pagos','url'=>route('finanzas.pagos.index')],['label'=>'Nuevo']]" />

<form method="POST" action="{{ route('finanzas.pagos.store') }}">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Datos del pago">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Alumno <span class="text-danger">*</span></label>
                    <select name="alumno_id" class="form-select @error('alumno_id') is-invalid @enderror" required>
                        <option value="">Seleccionar alumno...</option>
                        @foreach($alumnos as $a)
                        <option value="{{ $a->id }}" {{ old('alumno_id',request('alumno_id'))==$a->id?'selected':'' }}>
                            {{ $a->nombre_completo }} — {{ $a->matricula }}
                        </option>
                        @endforeach
                    </select>
                    @error('alumno_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Sede <span class="text-danger">*</span></label>
                    <select name="sede_id" class="form-select @error('sede_id') is-invalid @enderror" required>
                        @foreach($sedes as $s)
                        <option value="{{ $s->id }}" {{ old('sede_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Fecha de pago <span class="text-danger">*</span></label>
                    <input type="date" name="fecha_pago" class="form-control"
                           value="{{ old('fecha_pago', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Método de pago <span class="text-danger">*</span></label>
                    <select name="metodo_pago_id" class="form-select" required>
                        @foreach($metodos as $m)
                        <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Referencia</label>
                    <input type="text" name="referencia" class="form-control" value="{{ old('referencia') }}" placeholder="Folio/recibo">
                </div>
            </div>
        </x-ui.card>

        {{-- Cargos pendientes del alumno --}}
        @if(isset($alumnoSeleccionado))
        <x-ui.card title="Cargos pendientes" class="mt-3">
            @php $total = 0; @endphp
            @forelse($cargos as $i => $c)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div class="form-check">
                    <input type="checkbox" name="cargos[{{ $i }}][cargo_id]" value="{{ $c->id }}"
                           class="form-check-input" id="cargo_{{ $c->id }}">
                    <label class="form-check-label" for="cargo_{{ $c->id }}" style="font-size:.875rem">
                        {{ $c->concepto?->nombre }}
                    </label>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="hidden" name="cargos[{{ $i }}][importe_aplicado]" value="{{ $c->total }}">
                    <span class="fw-semibold">${{ number_format($c->total,2) }}</span>
                    <x-ui.badge :type="$c->estado==='vencido'?'danger':'warning'" small>{{ ucfirst($c->estado) }}</x-ui.badge>
                </div>
            </div>
            @php $total += $c->total; @endphp
            @empty
            <x-ui.empty-state message="Sin cargos pendientes para este alumno." />
            @endforelse
        </x-ui.card>
        @endif
    </div>
    <div class="col-md-4">
        <x-ui.card title="Resumen">
            <div class="mb-3">
                <label class="form-label fw-medium">Importe total <span class="text-danger">*</span></label>
                <input type="number" name="importe" class="form-control fw-bold" step="0.01" min="0.01"
                       value="{{ old('importe') }}" required placeholder="0.00">
            </div>
            <button type="submit" class="btn btn-success w-100">
                Registrar pago
            </button>
        </x-ui.card>
    </div>
</div>
</form>
</x-layouts.app>
```

**Agregar a PagoController:**
```php
public function create(Request $request): View
{
    $this->authorize('pagos.registrar');
    $orgId   = auth()->user()->organizacion_id;
    $alumnos = Alumno::where('organizacion_id',$orgId)->activos()->orderBy('apellido_paterno')->get();
    $metodos = MetodoPago::activos()->get();
    $sedes   = Sede::whereHas('organizacion',fn($q)=>$q->where('id',$orgId))->activas()->get();
    $alumnoSeleccionado = $request->alumno_id ? Alumno::find($request->alumno_id) : null;
    $cargos  = $alumnoSeleccionado
        ? Cargo::where('alumno_id',$alumnoSeleccionado->id)->whereIn('estado',['pendiente','parcial','vencido'])->get()
        : collect();
    return view('finanzas.pagos.create', compact('alumnos','metodos','sedes','alumnoSeleccionado','cargos'));
}
```

---

### 2. docentes/create y edit — Formulario docente

```blade
{{-- resources/views/docentes/create.blade.php --}}
<x-layouts.app page-title="Nuevo Docente">
<x-ui.page-header title="Nuevo Docente"
    :items="[['label'=>'Docentes','url'=>route('docentes.index')],['label'=>'Nuevo']]" />
<form method="POST" action="{{ route('docentes.store') }}">
@csrf
<div class="row g-3">
    <div class="col-md-8">
        <x-ui.card title="Datos del docente">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Usuario vinculado <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">Seleccionar usuario...</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id')==$u->id?'selected':'' }}>
                            {{ $u->nombre_completo }} — {{ $u->email }}
                        </option>
                        @endforeach
                    </select>
                    @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Número de empleado</label>
                    <input type="text" name="numero_empleado" class="form-control" value="{{ old('numero_empleado') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Especialidad</label>
                    <input type="text" name="especialidad" class="form-control" value="{{ old('especialidad') }}" placeholder="Ej: Matemáticas">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Cédula profesional</label>
                    <input type="text" name="cedula" class="form-control" value="{{ old('cedula') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Tipo de contrato <span class="text-danger">*</span></label>
                    <select name="tipo_contrato" class="form-select @error('tipo_contrato') is-invalid @enderror" required>
                        @foreach(['base'=>'Base','contrato'=>'Contrato','honorarios'=>'Honorarios','tiempo_parcial'=>'Tiempo parcial'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('tipo_contrato','contrato')===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Fecha de ingreso</label>
                    <input type="date" name="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-medium">Estatus <span class="text-danger">*</span></label>
                    <select name="estatus" class="form-select" required>
                        @foreach(['activo'=>'Activo','inactivo'=>'Inactivo','baja'=>'Baja'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('estatus','activo')===$v?'selected':'' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">Registrar docente</button>
    <a href="{{ route('docentes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
</form>
</x-layouts.app>
```

---

### 3. alumnos/inscripcion/create — Wizard de inscripción §22

```blade
{{-- resources/views/alumnos/inscripcion/create.blade.php --}}
<x-layouts.app page-title="Inscribir Alumno">
<x-ui.page-header title="Inscripción de alumno"
    :items="[['label'=>'Alumnos','url'=>route('alumnos.index')],['label'=>'Inscripción']]" />

<form method="POST" action="{{ route('alumnos.inscripcion.store') }}">
@csrf
<x-ui.card title="Datos de inscripción">
    <div class="row g-3">
        <div class="col-12">
            <label class="form-label fw-medium">Alumno <span class="text-danger">*</span></label>
            @if(isset($alumno))
                <input type="hidden" name="alumno_id" value="{{ $alumno->id }}">
                <div class="form-control bg-light">{{ $alumno->nombre_completo }} — {{ $alumno->matricula }}</div>
            @else
                <select name="alumno_id" class="form-select" required>
                    <option value="">Buscar alumno...</option>
                </select>
            @endif
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium">Ciclo escolar <span class="text-danger">*</span></label>
            <select name="ciclo_escolar_id" class="form-select @error('ciclo_escolar_id') is-invalid @enderror" required>
                <option value="">Seleccionar ciclo...</option>
                @foreach($ciclos as $c)
                <option value="{{ $c->id }}" {{ $c->es_actual?'selected':'' }}>{{ $c->nombre }}</option>
                @endforeach
            </select>
            @error('ciclo_escolar_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium">Sede <span class="text-danger">*</span></label>
            <select name="sede_id" class="form-select" required>
                @foreach($sedes as $s)
                <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium">Grado <span class="text-danger">*</span></label>
            <select name="grado_id" class="form-select" required>
                <option value="">Seleccionar grado...</option>
                @foreach($grados as $g)
                <option value="{{ $g->id }}">{{ $g->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-medium">Grupo <span class="text-danger">*</span></label>
            <select name="grupo_id" class="form-select" required>
                <option value="">Seleccionar grupo...</option>
                @foreach($grupos as $g)
                <option value="{{ $g->id }}">{{ $g->nombre }} ({{ $g->turno }})</option>
                @endforeach
            </select>
        </div>
    </div>
</x-ui.card>
<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-success">Inscribir alumno</button>
    @if(isset($alumno))
    <a href="{{ route('alumnos.show',$alumno) }}" class="btn btn-outline-secondary">Cancelar</a>
    @else
    <a href="{{ route('alumnos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    @endif
</div>
</form>
</x-layouts.app>
```

---

### 4. grupos/show — Ficha con alumnos y horario

```blade
{{-- resources/views/grupos/show.blade.php --}}
<x-layouts.app page-title="Grupo: {{ $grupo->nombre }}">
<x-ui.page-header title="{{ $grupo->nombre }} — {{ $grupo->grado?->nombre }}"
    :items="[['label'=>'Grupos','url'=>route('grupos.index')],['label'=>$grupo->nombre]]">
    <x-slot name="actions">
        @can('grupos.editar')
        <a href="{{ route('grupos.edit',$grupo) }}" class="btn btn-sm btn-outline-primary">Editar</a>
        @endcan
        @can('calificaciones.ver')
        <a href="{{ route('calificaciones.index',['grupo_id'=>$grupo->id]) }}" class="btn btn-sm btn-outline-info ms-1">Calificaciones</a>
        @endcan
        @can('asistencias.ver')
        <a href="{{ route('asistencias.index',['grupo_id'=>$grupo->id]) }}" class="btn btn-sm btn-outline-success ms-1">Asistencias</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<div class="row g-3">
    <div class="col-md-4">
        <x-ui.card title="Información">
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-5 text-muted">Grado</dt><dd class="col-7">{{ $grupo->grado?->nombre }}</dd>
                <dt class="col-5 text-muted">Turno</dt><dd class="col-7">{{ ucfirst($grupo->turno) }}</dd>
                <dt class="col-5 text-muted">Capacidad</dt><dd class="col-7">{{ $grupo->capacidad }}</dd>
                <dt class="col-5 text-muted">Aula</dt><dd class="col-7">{{ $grupo->aulaPrincipal?->nombre ?? '—' }}</dd>
                <dt class="col-5 text-muted">Tutor</dt><dd class="col-7">{{ $grupo->docenteTutor?->nombres ?? '—' }}</dd>
                <dt class="col-5 text-muted">Sede</dt><dd class="col-7">{{ $grupo->sede?->nombre }}</dd>
            </dl>
        </x-ui.card>
    </div>
    <div class="col-md-8">
        <x-ui.card title="Alumnos inscritos" :flush="true">
            <div class="table-responsive">
                <table class="table table-se table-sm mb-0">
                    <thead><tr><th>Alumno</th><th>Matrícula</th><th>Situación</th><th>Riesgo</th></tr></thead>
                    <tbody>
                        @forelse($grupo->alumnos ?? [] as $a)
                        <tr>
                            <td style="font-size:.8rem">
                                <a href="{{ route('alumnos.show',$a) }}" class="text-decoration-none fw-medium">{{ $a->nombre_completo }}</a>
                            </td>
                            <td style="font-size:.8rem">{{ $a->matricula }}</td>
                            <td><x-ui.badge :type="$a->situacion_academica==='regular'?'success':'warning'" small>{{ ucfirst($a->situacion_academica) }}</x-ui.badge></td>
                            <td><x-ui.badge :type="match($a->estatus_riesgo){'riesgo_alto'=>'danger','riesgo_medio'=>'warning','observacion'=>'info',default=>'success'}" small>{{ ucfirst(str_replace('_',' ',$a->estatus_riesgo)) }}</x-ui.badge></td>
                        </tr>
                        @empty
                        <tr><td colspan="4"><x-ui.empty-state message="Sin alumnos inscritos." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
```

---

## P2 — Python workers con datos reales

### estadisticas.py — Implementación con pandas

```python
# python/app/workers/estadisticas.py
import logging
import os
import pymysql
import pandas as pd

log = logging.getLogger("sistema_escolar.estadisticas")

def get_conn():
    return pymysql.connect(
        host=os.getenv("DB_HOST","127.0.0.1"),
        port=int(os.getenv("DB_PORT","3306")),
        user=os.getenv("DB_USER","root"),
        password=os.getenv("DB_PASS",""),
        database=os.getenv("DB_NAME","sistema_escolar"),
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor
    )

async def calcular(data: dict) -> dict:
    sede_id  = data.get("sede_id")
    ciclo_id = data.get("ciclo_id")
    conn = get_conn()

    try:
        df_cal = pd.read_sql(
            "SELECT resultado FROM calificaciones WHERE ciclo_escolar_id = %s",
            conn, params=[ciclo_id]
        )
        df_bajas = pd.read_sql(
            "SELECT tipo FROM bajas b JOIN alumnos a ON b.alumno_id=a.id WHERE a.sede_actual_id=%s",
            conn, params=[sede_id]
        )

        total   = len(df_cal)
        aprobados  = (df_cal["resultado"] == "aprobado").sum() if total > 0 else 0
        reprobados = (df_cal["resultado"] == "reprobado").sum() if total > 0 else 0
        deserciones= (df_bajas["tipo"] == "desercion").sum() if len(df_bajas) > 0 else 0

        return {
            "approval_rate":  round(aprobados/total*100,1) if total else 0,
            "failure_rate":   round(reprobados/total*100,1) if total else 0,
            "dropout_rate":   float(deserciones),
        }
    finally:
        conn.close()
```

**Agregar a requirements.txt:**
```
PyMySQL==1.1.1
```

---

## P3 — Tests básicos

### tests/Feature/Auth/LoginTest.php
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
        $this->post('/login',['email'=>$user->email,'password'=>'password'])
             ->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_credenciales_invalidas(): void
    {
        $user = User::factory()->create(['activo'=>true]);
        $this->post('/login',['email'=>$user->email,'password'=>'wrong'])
             ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_usuario_inactivo_bloqueado(): void
    {
        $user = User::factory()->create(['password'=>bcrypt('password'),'activo'=>false]);
        $this->post('/login',['email'=>$user->email,'password'=>'password'])
             ->assertRedirect('/login');
    }
}
```

### tests/Feature/RBAC/AutorizacionTest.php
```php
<?php
namespace Tests\Feature\RBAC;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutorizacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_docente_no_accede_auditoria(): void
    {
        // Crear docente con rol docente (sin auditoria.ver)
        $docente = User::factory()->create(['activo'=>true]);
        $this->actingAs($docente)->get('/auditoria')->assertStatus(403);
    }

    public function test_cajero_no_modifica_calificaciones(): void
    {
        $cajero = User::factory()->create(['activo'=>true]);
        $this->actingAs($cajero)->post('/calificaciones')->assertStatus(403);
    }
}
```

**Ejecutar:**
```bash
php artisan test
php artisan test --filter LoginTest
```

---

## P3 — API REST completa

### routes/api.php — ampliar:
```php
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $r) => $r->user());

    Route::apiResource('alumnos', AlumnoController::class)->only(['index','show']);
    Route::apiResource('calificaciones', CalificacionController::class)->only(['index','store']);
    Route::post('/asistencias', [AsistenciaController::class, 'store']);
    Route::get('/horarios', [HorarioController::class, 'index']);

    // Jobs Python — polling §78
    Route::get('/jobs/{jobId}', function (string $jobId) {
        $job = \App\Models\PythonJob::where('job_id',$jobId)->firstOrFail();
        return response()->json([
            'job_id'   => $job->job_id,
            'estado'   => $job->estado,
            'progreso' => $job->progreso,
            'resultado'=> $job->resultado,
        ]);
    });
});
```

---

## Resumen de prioridades actuales

| # | Vista/Feature | Archivo | Estado |
|---|--------------|---------|--------|
| 1 | `finanzas/pagos/create` | `resources/views/finanzas/pagos/create.blade.php` | ⚠️ stub |
| 2 | `docentes/create` + `edit` | `resources/views/docentes/` | ⚠️ stub |
| 3 | `alumnos/inscripcion/create` | completo arriba | ⚠️ stub |
| 4 | `grupos/show` con alumnos | completo arriba | ⚠️ stub |
| 5 | Python estadisticas con MySQL | `python/app/workers/estadisticas.py` | ⚠️ TODO |
| 6 | `rh/empleados/create` + `edit` | `resources/views/rh/empleados/` | ⚠️ stub |
| 7 | Tests Feature | `tests/Feature/` | 🔲 pendiente |
| 8 | API REST completa | `routes/api.php` | 🔲 pendiente |
| 9 | Redis como queue driver | `.env` | 🔲 producción |
