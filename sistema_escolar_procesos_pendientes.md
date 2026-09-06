# Sistema Escolar — Procesos Pendientes
## Guía paso a paso con código para implementar lo que falta
### Actualizado: 2026-09-05

---

## Estado antes de continuar

```
php artisan about          ✅ Laravel 12.69.1 / PHP 8.2.12
php artisan route:list     ✅ ~290 rutas sin errores fatales
php artisan view:cache     ✅ Blade templates cached
npm run build              ✅ Bootstrap CSS 237KB + JS 137KB
```

**Errores corregidos:**
- `$this->middleware()` en constructores → eliminado (Laravel 12 no lo soporta)
- 151 vistas con `@extends` → convertidas a `<x-layouts.app>`
- BOM UTF-8 en PHP → eliminado con WriteAllBytes
- Namespaces incorrectos en controllers → corregidos
- `maatwebsite/excel` no instalado → reemplazado por CSV nativo
- Modelo `Reporte` inexistente → creado sobre `python_jobs`
- Servicios duplicados → eliminados los de la raíz

---

## PROCESO 1 — Vista alumnos/show completa §20-§22

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
        <a href="{{ route('bajas.create',['alumno_id'=>$alumno->id]) }}" class="btn btn-sm btn-outline-danger">Baja</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<div class="row g-3">
    <div class="col-md-4">
        <x-ui.card title="Datos personales">
            <dl class="row mb-0" style="font-size:.875rem">
                <dt class="col-5 text-muted">Matricula</dt><dd class="col-7">{{ $alumno->matricula ?? '—' }}</dd>
                <dt class="col-5 text-muted">CURP</dt><dd class="col-7">{{ $alumno->curp ?? '—' }}</dd>
                <dt class="col-5 text-muted">Nacimiento</dt><dd class="col-7">{{ $alumno->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</dd>
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
            @empty
            <x-ui.empty-state message="Sin tutores." />
            @endforelse
        </x-ui.card>
    </div>
    <div class="col-md-8">
        <x-ui.card :flush="true">
            <ul class="nav nav-tabs px-3 pt-2" id="tabsAlumno">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-tray">Trayectoria</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-bajas">Bajas</button></li>
            </ul>
            <div class="tab-content p-3">
                <div class="tab-pane fade show active" id="tab-tray">
                    @forelse($alumno->trayectorias->sortByDesc('fecha_inicio') as $t)
                    <div class="d-flex justify-content-between py-2 border-bottom" style="font-size:.875rem">
                        <span>{{ $t->cicloEscolar?->nombre }} — {{ $t->grado?->nombre }} {{ $t->grupo?->nombre }}</span>
                        <x-ui.badge type="secondary" small>{{ $t->estatus }}</x-ui.badge>
                    </div>
                    @empty
                    <x-ui.empty-state message="Sin trayectoria registrada." />
                    @endforelse
                </div>
                <div class="tab-pane fade" id="tab-bajas">
                    @forelse($alumno->bajas as $b)
                    <div class="py-2 border-bottom" style="font-size:.875rem">
                        <strong>{{ ucfirst($b->tipo) }}</strong> — {{ $b->fecha_solicitud?->format('d/m/Y') }}
                        <p class="mb-0 text-muted">{{ $b->motivo }}</p>
                    </div>
                    @empty
                    <x-ui.empty-state message="Sin bajas registradas." />
                    @endforelse
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
</x-layouts.app>
```

**Controller — AlumnoController::show() a actualizar:**
```php
public function show(int $id): View
{
    $this->authorize('alumnos.ver');
    $alumno = Alumno::where('organizacion_id', auth()->user()->organizacion_id)
        ->with(['trayectorias.cicloEscolar','trayectorias.grado','trayectorias.grupo','tutores','bajas'])
        ->findOrFail($id);
    return view('alumnos.show', compact('alumno'));
}
```

---

## PROCESO 2 — Vista calificaciones/index (cuadrícula) §41

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
                    <th style="min-width:180px">Alumno</th>
                    @foreach($materias as $m)<th class="text-center" style="min-width:80px">{{ Str::limit($m->nombre,12) }}</th>@endforeach
                    <th class="text-center">Promedio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alumnos as $alumno)
                <tr>
                    <td class="fw-medium" style="font-size:.8rem">{{ $alumno->nombre_completo }}</td>
                    @foreach($materias as $m)
                    @php $cal = $calificaciones[$alumno->id][$m->id] ?? null; @endphp
                    <td class="text-center" style="font-size:.85rem">
                        @if($cal)
                            <span class="{{ $cal->resultado==='reprobado'?'text-danger fw-bold':'' }}">{{ $cal->calificacion ?? '—' }}</span>
                        @else
                            @can('calificaciones.registrar')
                            <a href="{{ route('calificaciones.create',['alumno_id'=>$alumno->id,'materia_id'=>$m->id,'periodo_id'=>request('periodo_id')]) }}" class="btn btn-link btn-sm p-0 text-muted">+</a>
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
<x-ui.card><x-ui.empty-state message="Selecciona un grupo y periodo para ver las calificaciones." /></x-ui.card>
@endif
</x-layouts.app>
```

**CalificacionController::index() — implementación completa:**
```php
public function index(Request $request): View
{
    $this->authorize('calificaciones.ver');
    $orgId     = auth()->user()->organizacion_id;
    $grupoId   = $request->grupo_id;
    $periodoId = $request->periodo_id;

    $grupos   = Grupo::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->activos()->get();
    $periodos = PeriodoEvaluacion::whereHas('cicloEscolar', fn($q) => $q->where('organizacion_id', $orgId))->get();

    $alumnos = collect(); $materias = collect();
    $calificaciones = []; $promedios = [];

    if ($grupoId && $periodoId) {
        $grupo    = Grupo::findOrFail($grupoId);
        $alumnos  = Alumno::where('sede_actual_id', $grupo->sede_id)->activos()->get();
        $materias = Materia::whereHas('docenteGrupoMaterias', fn($q) => $q->where('grupo_id', $grupoId))->get();

        $cals = Calificacion::where('grupo_id', $grupoId)
            ->where('periodo_evaluacion_id', $periodoId)->get();

        foreach ($cals as $c) {
            $calificaciones[$c->alumno_id][$c->materia_id] = $c;
        }
        foreach ($alumnos as $a) {
            $vals = $cals->where('alumno_id', $a->id)->pluck('calificacion')->filter();
            $promedios[$a->id] = $vals->count() ? round($vals->avg(), 1) : null;
        }
    }
    return view('calificaciones.index', compact('grupos','periodos','alumnos','materias','calificaciones','promedios'));
}
```

---

## PROCESO 3 — Vista asistencias/index (pase de lista) §39

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
<x-ui.card title="Lista — {{ request('fecha') }}">
<form method="POST" action="{{ route('asistencias.store') }}">
    @csrf
    <input type="hidden" name="grupo_id" value="{{ request('grupo_id') }}">
    <input type="hidden" name="fecha" value="{{ request('fecha') }}">
    <input type="hidden" name="ciclo_id" value="{{ $cicloActual?->id }}">
    <div class="table-responsive">
        <table class="table table-se mb-0">
            <thead><tr><th>#</th><th>Alumno</th><th>Estado</th><th>Observacion</th></tr></thead>
            <tbody>
                @foreach($alumnos as $i => $alumno)
                @php $prev = $asistenciasExistentes[$alumno->id] ?? null; @endphp
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td class="fw-medium" style="font-size:.875rem">{{ $alumno->nombre_completo }}</td>
                    <td>
                        <input type="hidden" name="lista[{{ $i }}][alumno_id]" value="{{ $alumno->id }}">
                        <select name="lista[{{ $i }}][estado]" class="form-select form-select-sm" style="width:140px">
                            @foreach(['presente'=>'Presente','falta'=>'Falta','retardo'=>'Retardo','justificada'=>'Justificada'] as $v=>$l)
                            <option value="{{ $v }}" {{ ($prev->estado ?? 'presente') === $v ? 'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="lista[{{ $i }}][observacion]" class="form-control form-control-sm" value="{{ $prev->observacion ?? '' }}" placeholder="Opcional"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3 px-1 pb-2">
        <button type="submit" class="btn btn-primary btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
            Guardar pase
        </button>
    </div>
</form>
</x-ui.card>
@elseif(request('grupo_id'))
<x-ui.card><x-ui.empty-state message="No hay alumnos activos en este grupo." /></x-ui.card>
@endif
</x-layouts.app>
```

**AsistenciaController::index() y store():**
```php
public function index(Request $request): View
{
    $this->authorize('asistencias.ver');
    $orgId  = auth()->user()->organizacion_id;
    $grupos = Grupo::whereHas('sede', fn($q) => $q->where('organizacion_id', $orgId))->activos()->get();
    $alumnos = collect(); $asistenciasExistentes = [];

    if ($request->grupo_id) {
        $grupo   = Grupo::find($request->grupo_id);
        $alumnos = Alumno::where('sede_actual_id', $grupo?->sede_id)->activos()->get();
        $fecha   = $request->fecha ?? now()->format('Y-m-d');
        $asistenciasExistentes = Asistencia::where('grupo_id', $request->grupo_id)
            ->where('fecha', $fecha)->get()->keyBy('alumno_id')->toArray();
    }
    $cicloActual = CicloEscolar::where('es_actual', true)->first();
    return view('asistencias.index', compact('grupos','alumnos','asistenciasExistentes','cicloActual'));
}

public function store(Request $request): RedirectResponse
{
    $this->authorize('asistencias.registrar');
    app(\App\Services\Academico\AsistenciaService::class)->registrarLista(
        grupoId:   (int) $request->grupo_id,
        materiaId: (int) ($request->materia_id ?? 0),
        docenteId: auth()->id(),
        cicloId:   (int) $request->ciclo_id,
        fecha:     $request->fecha,
        lista:     $request->lista ?? [],
        userId:    auth()->id()
    );
    return back()->with('success', 'Pase de lista guardado correctamente.');
}
```

---

## PROCESO 4 — Vista users/index (CRUD usuarios) §14-§15

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
            <thead>
                <tr><th>Usuario</th><th>Email</th><th>Roles</th><th>Sede principal</th><th>Estado</th><th>Último acceso</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <x-ui.avatar :name="$u->nombre_completo" size="sm" />
                            <div>
                                <div class="fw-medium" style="font-size:.875rem">{{ $u->nombre_completo }}</div>
                                <div class="text-muted" style="font-size:.75rem">@{{ $u->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:.8rem">{{ $u->email }}</td>
                    <td>
                        @foreach($u->roles->take(2) as $r)
                        <x-ui.badge type="secondary" :small="true">{{ $r->nombre }}</x-ui.badge>
                        @endforeach
                    </td>
                    <td style="font-size:.8rem">{{ $u->sedePrincipal()?->nombre ?? '—' }}</td>
                    <td>
                        <x-ui.badge :type="$u->activo?'success':'secondary'">{{ $u->activo?'Activo':'Inactivo' }}</x-ui.badge>
                    </td>
                    <td style="font-size:.78rem">{{ $u->ultimo_acceso_at?->diffForHumans() ?? 'Nunca' }}</td>
                    <td class="text-end">
                        @can('usuarios.editar')
                        <a href="{{ route('users.edit',$u) }}" class="btn btn-sm btn-outline-primary">Editar</a>
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

## PROCESO 5 — Vista finanzas/cargos/index §48

**Archivo:** `resources/views/finanzas/cargos/index.blade.php`

```blade
<x-layouts.app page-title="Cargos">
<x-ui.page-header title="Cargos de alumnos">
    <x-slot name="actions">
        @can('pagos.registrar')
        <a href="{{ route('finanzas.cargos.store') }}" class="btn btn-sm btn-primary">+ Nuevo cargo</a>
        @endcan
    </x-slot>
</x-ui.page-header>
<x-ui.filter-bar :action="route('finanzas.cargos.index')">
    <x-slot name="fields">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control form-control-sm" placeholder="Alumno, referencia..." value="{{ request('q') }}">
        </div>
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
                    <td>
                        <x-ui.badge :type="match($c->estado){'pendiente'=>'warning','pagado'=>'success','cancelado'=>'secondary','vencido'=>'danger',default=>'info'}">
                            {{ ucfirst($c->estado) }}
                        </x-ui.badge>
                    </td>
                    <td>
                        <a href="{{ route('alumnos.show',$c->alumno_id) }}" class="btn btn-sm btn-outline-secondary">Ver alumno</a>
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

## PROCESO 6 — Recuperación de contraseña §63

**Rutas a agregar en `routes/web.php`:**
```php
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password',           [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password',          [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}',    [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password',           [PasswordResetController::class, 'update'])->name('password.update');
});
```

**`app/Http/Controllers/Auth/PasswordResetController.php`:**
```php
<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Password};
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function create() { return view('auth.forgot-password'); }

    public function store(Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function edit(string $token) { return view('auth.reset-password', ['token' => $token]); }

    public function update(Request $request) {
        $request->validate(['token'=>'required','email'=>'required|email','password'=>'required|min:8|confirmed']);
        $status = Password::reset(
            $request->only('email','password','password_confirmation','token'),
            function ($user, $password) {
                $user->forceFill(['password'=>Hash::make($password)])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}
```

**`resources/views/auth/forgot-password.blade.php`:**
```blade
<x-layouts.guest title="Recuperar contraseña">
<div class="login-card">
    <div class="text-center mb-4">
        <h5 class="fw-bold">Recuperar contraseña</h5>
        <p class="text-muted" style="font-size:.875rem">Ingresa tu email y te enviaremos un enlace.</p>
    </div>
    @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-medium">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Enviar enlace</button>
    </form>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none" style="font-size:.875rem">Volver al login</a>
    </div>
</div>
</x-layouts.guest>
```

---

## PROCESO 7 — Infraestructura Python §3, §76-§78, §109

**Crear directorio y archivos:**

```bash
# Ejecutar desde la raíz del proyecto
mkdir python\app\workers
mkdir python\tests
```

**`python/requirements.txt`:**
```
fastapi==0.115.0
uvicorn==0.30.6
pandas==2.2.3
openpyxl==3.1.5
python-multipart==0.0.12
httpx==0.27.2
```

**`python/app/main.py`:**
```python
from fastapi import FastAPI, Request, HTTPException
import os

app = FastAPI(title="Sistema Escolar - Python Worker", version="1.0")
SECRET = os.getenv("PYTHON_SERVICE_SECRET", "")

@app.middleware("http")
async def auth_middleware(request: Request, call_next):
    if request.url.path not in ["/", "/docs"] and request.headers.get("X-Python-Secret") != SECRET:
        raise HTTPException(status_code=401, detail="Unauthorized")
    return await call_next(request)

@app.get("/")
async def health(): return {"status": "ok"}

@app.post("/jobs/estadisticas")
async def estadisticas(data: dict):
    # TODO: calcular con pandas desde los datos enviados
    return {"job_id": data.get("job_id"), "status": "completed", "results": {"approval_rate": 0, "failure_rate": 0}}

@app.post("/jobs/riesgo")
async def riesgo(data: dict):
    # TODO: motor predictivo con scikit-learn
    return {"job_id": data.get("job_id"), "status": "completed", "results": {}}

@app.post("/jobs/importacion")
async def importacion(data: dict):
    # TODO: procesar Excel/CSV con pandas
    return {"job_id": data.get("job_id"), "status": "completed", "results": {"procesados": 0, "errores": 0}}

@app.post("/jobs/reporte")
async def reporte(data: dict):
    # TODO: generar PDF/Excel con reportlab/openpyxl
    return {"job_id": data.get("job_id"), "status": "completed", "results": {"archivo": ""}}

@app.post("/jobs/horario")
async def horario(data: dict):
    # TODO: algoritmo de optimización de horarios
    return {"job_id": data.get("job_id"), "status": "completed", "results": {"horario": []}}
```

**Agregar a `.env`:**
```
PYTHON_SERVICE_URL=http://localhost:8001
PYTHON_SERVICE_SECRET=cambiar-en-produccion
```

**Ejecutar Python:**
```bash
cd python
pip install -r requirements.txt
uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload
```

---

## PROCESO 8 — API REST /api/v1/ §89

**Agregar a `routes/api.php`:**
```php
use App\Http\Controllers\Alumnos\AlumnoController;
use App\Models\PythonJob;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $r) => $r->user());
    Route::get('/alumnos', [AlumnoController::class, 'index']);
    Route::get('/alumnos/{id}', [AlumnoController::class, 'show']);
    Route::post('/calificaciones', [\App\Http\Controllers\CalificacionController::class, 'store']);
    Route::post('/asistencias', [\App\Http\Controllers\AsistenciaController::class, 'store']);
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

---

## PROCESO 9 — Dashboard enriquecido por rol §73-§75

**`routes/web.php` — cambiar el closure de dashboard:**
```php
Route::get('/dashboard', function () {
    $user    = auth()->user();
    $orgId   = $user->organizacion_id;
    $sedeId  = $user->sedePrincipal()?->id;
    $ciclo   = \App\Models\CicloEscolar::where('es_actual', true)->first();

    $indicadores = [];
    $riesgo      = [];

    if ($sedeId && $ciclo) {
        $indicadores = app(\App\Services\Academico\IndicadoresService::class)
            ->calcularIndicadoresSede($sedeId, $ciclo->id);
        $riesgo = app(\App\Services\Academico\RiesgoAcademicoService::class)
            ->calcularMasivo($sedeId, $ciclo->id);
    }

    return view('dashboard', compact('indicadores', 'riesgo', 'ciclo'));
})->name('dashboard');
```

**`resources/views/dashboard.blade.php` — stat cards reales:**
```blade
{{-- Reemplazar el bloque de stat cards existente --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <x-ui.stat-card label="Alumnos activos" :value="$indicadores['inscritos'] ?? '—'" color="primary" :link="route('alumnos.index')" />
    </div>
    <div class="col-6 col-xl-3">
        <x-ui.stat-card label="% Aprobación" :value="($indicadores['pct_aprobacion'] ?? '—').'%'" color="success" />
    </div>
    <div class="col-6 col-xl-3">
        <x-ui.stat-card label="En riesgo" :value="($riesgo['riesgo_alto'] ?? 0) + ($riesgo['riesgo_medio'] ?? 0)" color="danger" :link="route('alumnos.index')" />
    </div>
    <div class="col-6 col-xl-3">
        <x-ui.stat-card label="% Deserción" :value="($indicadores['pct_desercion'] ?? '—').'%'" color="warning" />
    </div>
</div>
```

---

## PROCESO 10 — Tests básicos §97-§98

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

    public function test_login_exitoso(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password'), 'activo' => true]);
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertRedirect('/dashboard');
    }

    public function test_login_credenciales_invalidas(): void
    {
        $user = User::factory()->create(['activo' => true]);
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
             ->assertSessionHasErrors('email');
    }

    public function test_usuario_inactivo_no_puede_entrar(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password'), 'activo' => false]);
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
             ->assertSessionHasErrors();
    }
}
```

**Ejecutar tests:**
```bash
php artisan test --filter LoginTest
php artisan test
```

---

## RESUMEN PRIORIDADES

| # | Proceso | Prioridad | Tiempo |
|---|---------|-----------|--------|
| 1 | Vista alumnos/show completa | P1 | 1h |
| 2 | Vista calificaciones (cuadrícula) | P1 | 1.5h |
| 3 | Vista asistencias (pase de lista) | P1 | 1h |
| 4 | Vista usuarios CRUD | P1 | 1h |
| 5 | Vista finanzas/cargos | P1 | 1h |
| 6 | Recuperación de contraseña | P2 | 0.5h |
| 7 | Dashboard con indicadores reales | P2 | 1h |
| 8 | Infraestructura Python base | P2 | 2h |
| 9 | API REST /api/v1/ | P3 | 1.5h |
| 10 | Tests básicos | P3 | 1h |
