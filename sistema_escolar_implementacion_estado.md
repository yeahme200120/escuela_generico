# 📚 Sistema Escolar Multisede — Estado de Implementación
## Bitácora técnica de desarrollo activo
### Laravel 12 + Bootstrap 5 + Blade + MySQL + Python

**Versión doc:** 4.0  
**Última actualización:** 2026-09-04  
**Estado general:** Backend núcleo completo · Frontend parcial · Python pendiente  
**Spec de referencia:** `sistema_escolar_laravel_blade_livewire_python_v3.md`

---

## Stack definitivo

| Capa | Tecnología | Versión | Estado |
|------|-----------|---------|--------|
| Backend | Laravel | 12.69.1 | ✅ |
| PHP | PHP | 8.2.12 | ✅ |
| Base de datos | MySQL | 8.x XAMPP | ✅ 34 migraciones |
| Autenticación | Sanctum | 4.3 | ✅ |
| Frontend CSS | Bootstrap | 5.3.8 | ✅ compilado |
| Frontend JS | Bootstrap JS + Vanilla | 5.3.8 | ✅ compilado |
| Frontend HTML | Blade | nativo | ✅ |
| Build | Vite + laravel-vite-plugin | 6.x / 1.2 | ✅ |
| RBAC | Custom (sin paquete) | — | ✅ |
| Queue | Database driver | — | ✅ |
| Python / FastAPI | — | 3.12+ | 🔲 directorio no creado |
| Redis | — | — | 🔲 pendiente producción |

> **Sin Livewire. Sin Alpine.js. Sin Tailwind.**  
> Stack frontend: Bootstrap 5 nativo + Bootstrap JS + Vanilla JS puro.

---

## Conteo actual verificado

| Artefacto | Cantidad |
|-----------|---------|
| Migraciones ejecutadas | **34** |
| Tablas en MySQL | **59+** |
| Modelos Eloquent | **67** |
| Controllers | **17** |
| Services | **16** |
| Jobs | **1** |
| Policies | **5** |
| Seeders | **5** |
| Rutas registradas | **50** |
| Vistas Blade (completas) | **4** (auditoría + login + apariencia) |
| Vistas Blade (stubs) | **18** |
| Componentes UI | **6** |
| Configs propias | **3** (audit, theme, python) |

---

## Base de datos — 34 migraciones · 59+ tablas

| Grupo | Tablas |
|-------|--------|
| Infraestructura Laravel | cache, cache_locks, jobs, job_batches, failed_jobs, sessions, personal_access_tokens |
| Organización | organizaciones, escuelas, sedes, edificios, aulas |
| Estructura académica | ciclos_escolares, niveles_educativos, grados, grupos, materias, planes_estudio, plan_materias |
| Usuarios y RBAC | users, password_reset_tokens, roles, permissions, role_permissions, user_roles, user_permissions, user_sedes, system_settings |
| Trazabilidad | user_sessions, access_logs, audit_logs, query_logs |
| Alumnos y trayectoria | alumnos, tutores, alumno_tutor, docentes, docente_grupo_materia, trayectorias_alumno, alumno_grupo_historial, bajas, reingresos |
| Operación académica | horario_bloques, horarios, periodos_asistencia, asistencias, justificantes, periodos_evaluacion, calificaciones, regularizaciones |
| Control escolar | tipos_documento, documentos, folios |
| Finanzas | conceptos_pago, cargos, parcialidades, metodos_pago, pagos, pago_detalle |
| Caja | cajas, turnos_caja, movimientos_caja |
| RH | empleados, contratos, asistencia_personal |
| Inventario | categorias_inventario, inventario, movimientos_inventario, activos_fijos |
| Comunicación | notificaciones, notificacion_usuario, calendario_escolar |
| Admisiones | prospectos, seguimientos_prospecto, admisiones |
| Mantenimiento | mantenimiento |
| Integraciones | webhook_events, python_jobs |

---

## Modelos Eloquent — 67 modelos

### Núcleo organizacional (9)
`Organizacion` `Escuela` `Sede` `Edificio` `Aula` `CicloEscolar` `NivelEducativo` `Grado` `Grupo`

### Usuarios y RBAC (9)
`User` `Role` `Permission` `UserRole` `UserPermission` `UserSede` `SystemSetting` `UserSession` `PeriodoAsistencia`

### Trazabilidad (4)
`AccessLog` `AuditLog` `QueryLog` `PythonJob`

### Alumnos y trayectoria (9)
`Alumno` `Tutor` `Docente` `DocenteGrupoMateria` `TrayectoriaAlumno` `AlumnoGrupoHistorial` `Baja` `Reingreso` `PlanEstudio` · `PlanMateria` `Materia`

### Operación académica (7)
`HorarioBloque` `Horario` `Asistencia` `Justificante` `PeriodoEvaluacion` `Calificacion` `Regularizacion`

### Control escolar (4)
`TipoDocumento` `Documento` `Folio`

### Finanzas y caja (10)
`ConceptoPago` `Cargo` `Parcialidad` `MetodoPago` `Pago` `PagoDetalle` `Caja` `TurnoCaja` `MovimientoCaja`

### RH (3)
`Empleado` `Contrato` `AsistenciaPersonal`

### Inventario (4)
`CategoriaInventario` `Inventario` `MovimientoInventario` `ActivoFijo`

### Comunicación y calendario (2)
`Notificacion` `CalendarioEscolar`

### Admisiones (3)
`Prospecto` `SeguimientoProspecto` `Admision`

### Mantenimiento e integraciones (3)
`Mantenimiento` `WebhookEvent`

### Traits propios
| Trait | Archivo | Función |
|-------|---------|---------|
| `HasUuid` | `app/Models/Concerns/HasUuid.php` | UUID v4 auto en `creating`, `findByUuid()` |
| `Auditable` | `app/Models/Concerns/Auditable.php` | Captura before/after en create/update/delete → `AuditService::logModel()`. `withoutAudit(fn)` para bypass |

---

## Services — 16 archivos

| Service | Ruta | Estado | Métodos clave |
|---------|------|--------|--------------|
| `AuditService` | `Services/Auditoria/` | ✅ | `log()` `logModel()` `logAccess()` `logError()` `logUnauthorized()` |
| `QueryLogger` | `Services/Auditoria/` | ✅ | `startListening()` `flush()` buffer 200 queries |
| `AuthService` | `Services/Auth/` | ✅ | `login()` `logout()` `revocarSesion()` `revocarOtrasSesiones()` |
| `LoginResult` | `Services/Auth/` | ✅ | Value object: success\|failed\|blocked |
| `ThemeService` | `Services/Configuracion/` | ✅ | `getForOrganizacion()` cache 30min · `saveForOrganizacion()` valida hex |
| `PagoService` | `Services/Finanzas/` | ✅ | `registrar()` idempotencia SHA-256 · `cancelar()` reversión cargos |
| `CajaService` | `Services/Finanzas/` | ✅ | `abrir()` `cerrar()` monto_esperado · `registrarMovimiento()` |
| `DocumentoService` | `Services/Documentos/` | ✅ | `generarFolio()` lockForUpdate · `crear()` · `autorizar()` |
| `AsistenciaService` | `Services/Academico/` | ✅ | `registrarLista()` · `aplicarJustificante()` sin borrar original · `calcularPorcentaje()` |
| `CalificacionService` | `Services/Academico/` | ✅ | `registrar()` · `cerrarPeriodo()` lock |
| `HorarioConflictService` | `Services/Academico/` | ✅ | `verificar()` 5 tipos de colisión · `sinConflictos()` |
| `RiesgoAcademicoService` | `Services/Academico/` | ✅ | `calcular()` motor de reglas 4 niveles · `calcularMasivo()` chunk |
| `IndicadoresService` | `Services/Academico/` | ✅ | `calcularIndicadoresSede()` → % aprobación/deserción/permanencia |
| `BajaService` | `Services/Alumnos/` | ✅ | `registrarBaja()` preserva historial · `procesarReingreso()` |
| `PythonJobService` | `Services/Python/` | ✅ | `despachar()` `ejecutar()` HTTP FastAPI · `obtenerEstado()` |
| `WebhookService` | `Services/Webhook/` | ✅ | `despachar()` idempotencia · `enviar()` HMAC · `verificarFirma()` reintentos |

---

## Controllers — 17 archivos

| Controller | Namespace | Acciones implementadas |
|-----------|-----------|----------------------|
| `LoginController` | `Auth` | `index` `store` (geo+device campos hidden) |
| `LogoutController` | `Auth` | `__invoke` → AuthService::logout() |
| `AuditLogController` | `Auditoria` | `index` filtros completos + modal before/after |
| `AccessLogController` | `Auditoria` | `index` anomalías destacadas + solo_anomalias |
| `SesionesController` | `Auditoria` | `index` `destroy` revocar (protege sesión propia) |
| `QueryLogController` | `Auditoria` | `index` filtros + SQL modal + lentas |
| `AparienciaController` | `Configuracion` | `index` `update` → ThemeService + authorize |
| `AlumnoController` | `Alumnos` | CRUD completo + authorize + audit |
| `InscripcionController` | `Alumnos` | `store` DB::transaction trayectoria+historial |
| `CargoController` | `Finanzas` | `index` `store` |
| `PagoController` | `Finanzas` | `index` `store` `destroy` → PagoService |
| `CajaController` | `Finanzas` | `index` `abrir` `cerrar` → CajaService |
| `EmpleadoController` | `RH` | `index` `create` `store` `show` `edit` `update` |
| `InventarioController` | `Inventario` | `index` `store` `movimiento` (entrada/salida/ajuste) |
| `ProspectoController` | `Admisiones` | `index` `store` `show` `seguimiento` |
| `DocumentoController` | `Documentos` | `index` `store` `autorizar` → DocumentoService |

---

## Jobs — 1 archivo

| Job | Archivo | Función |
|-----|---------|---------|
| `DispatchPythonJob` | `app/Jobs/` | `ShouldQueue` · llama PythonJobService::ejecutar() · `tries=3` `timeout=600` |

---

## Policies — 5 archivos

| Policy | Modelo | Reglas clave |
|--------|--------|-------------|
| `BasePolicy` | base | `before()`: superadmin pasa, inactivo/bloqueado deniega |
| `UserPolicy` | `User` | Jerarquía de niveles, misma org, no autoeliminarse |
| `AuditoriaPolicy` | `AuditLog` | `create/update/delete = false` siempre. Inmutable |
| `SystemSettingPolicy` | `SystemSetting` | `theme.*` solo `configuracion.apariencia.editar` |
| `SedePolicy` | `Sede` | Scope por org + userSedes, no eliminar físicamente |

**Gate dinámico — AppServiceProvider:**
```php
$this->authorize('alumnos.ver');         // en controllers
$this->authorize('calificaciones.registrar');
@can('documentos.autorizar') @endcan    // en Blade
Gate::allows('pagos.cancelar');          // en código
```

---

## Seeders ejecutados

| Seeder | Datos creados |
|--------|--------------|
| `RolesPermisosSeeder` | 11 roles del sistema + 53 permisos + asignación rol→permisos |
| `OrganizacionSeeder` | Org "Institución Educativa Demo" + Escuela + Sede Norte + Edificio A + 4 aulas + Secundaria + 3 grados + Ciclo 2026-2027 |
| `SuperadminSeeder` | 4 usuarios: superadmin / directivo / docente / cajero · contraseña: `Admin@2026!` |
| `MateriaSeeder` | 10 materias base para Colegio Demo |

**Credenciales de acceso:**
| Email | Username | Rol |
|-------|----------|-----|
| superadmin@sistema.mx | superadmin | Superadministrador |
| directivo@sistema.mx | directivo | Directivo |
| docente@sistema.mx | docente | Docente |
| cajero@sistema.mx | cajero | Cajero |

---

## Trazabilidad — Flujo completo implementado

```
Request HTTP entrante
    ↓
SetRequestId      → REQ-{ULID} → RequestContext + header respuesta X-Request-ID
    ↓
GeoTrace          → GeoContext desde X-Geo-* / X-Device-* headers
                    Puebla RequestContext · Inicia QueryLogger.startListening()
    ↓
CheckUserActive   → activo=true y no bloqueado (si autenticado)
    ↓
Controller / Vista
    ↓
GeoTrace (fin)    → QueryLogger.flush() → persiste hasta 200 queries en query_logs
                    Actualiza UserSession.last_seen_at
```

**En login** (sin sesión aún): geo y device van como `campos hidden` del formulario:
`geo_latitude`, `geo_longitude`, `geo_accuracy`, `geo_altitude`, `geo_source`, `device_id`, `device_info`

**Anomalías detectadas automáticamente:**
- `nuevo_dispositivo` → device_id nunca visto
- `nueva_ubicacion` → distancia > 50 km vs último acceso
- `fuera_geocerca` → fuera del radio_geocerca_metros de la sede
- `fuera_de_horario` → hora < 6am o >= 11pm
- `viaje_imposible` → velocidad > 900 km/h (fórmula Haversine)

**JS disponible en `window`:**
```js
window.GeoCapture.getPosition()  // Promise {latitude, longitude, accuracy, source}
window.DeviceInfo.get()          // {screen, timezone, user_agent, platform, ...}
window.DeviceInfo.getId()        // Promise → SHA-256 fingerprint string
```

---

## Rutas — 50 registradas (sin errores)

```
GET/POST  /login           /logout(POST)       /dashboard
/alumnos  (resource completo)
/alumnos/inscripcion (POST)
/finanzas/cargos     /finanzas/pagos    /finanzas/caja
/auditoria (4: index, accesos, sesiones, queries)
/configuracion/apariencia
/rh/empleados (resource)
/inventario          /admisiones/prospectos
/documentos
/api/v1/user
```

---

## Vistas Blade — Estado real

### ✅ Completas y funcionales

| Vista | Descripción |
|-------|-------------|
| `auth/login.blade.php` | Bootstrap 5, captura geo/device en JS, toggle password, indicador geo |
| `auditoria/index.blade.php` | Tabla paginada, filtros, modal before/after JSON, geo indicator |
| `auditoria/accesos.blade.php` | Badges anomalías (viaje imposible/geocerca/nuevo dispositivo/horario) |
| `auditoria/sesiones.blade.php` | Sesiones activas, botón revocar con confirmación, protege sesión propia |
| `auditoria/queries.blade.php` | SQL expandible en modal, queries lentas en amarillo |
| `configuracion/apariencia.blade.php` | Color pickers + inputs hex sincronizados, preview en vivo CSS vars, reset defaults |
| `dashboard.blade.php` | Stat cards Bootstrap, badges de estado por fase |
| `components/layouts/app.blade.php` | Sidebar + topbar + flash + @stack |
| `components/layouts/guest.blade.php` | Fondo degradado Bootstrap |
| `partials/sidebar.blade.php` | Bootstrap collapse sin Alpine, chevron animado, menú por permisos |
| `partials/topbar.blade.php` | Toggle sidebar, dropdown usuario, Request ID debug, sede activa |
| `partials/dynamic-theme.blade.php` | Variables CSS --se-* desde system_settings |

### ⚠️ Stubs (existen pero son placeholders de 1 línea)

```
alumnos/index create edit show
admisiones/prospectos/index show
finanzas/cargos/index  finanzas/pagos/index  finanzas/caja/index
inventario/index
rh/empleados/index create edit show
documentos/index
```

### Componentes UI — `components/ui/`

| Componente | Props | ✅/🔲 |
|-----------|-------|------|
| `x-ui.badge` | type, pill, small | ✅ |
| `x-ui.alert` | type, dismissible, icon | ✅ |
| `x-ui.card` | title, actions(slot), footer, flush | ✅ |
| `x-ui.empty-state` | message, icon, action(slot) | ✅ |
| `x-ui.page-header` | title, subtitle, actions(slot) | ✅ |
| `x-ui.filter-bar` | action, method, fields(slot) | ✅ |
| `x-ui.table` | — | 🔲 |
| `x-ui.modal` | — | 🔲 |
| `x-ui.confirm` | — | 🔲 |
| `x-ui.stat-card` | — | 🔲 |
| `x-ui.breadcrumb` | — | 🔲 |

---

## Configs propias del sistema

| Archivo | Contenido |
|---------|-----------|
| `config/audit.php` | enabled, store_queries, slow_query_ms, max_login_attempts, lockout_minutes, max_travel_speed_kmh, retention_days |
| `config/theme.php` | Colores default (primary/secondary/success/warning/danger/info/background/surface/text), logo, favicon |
| `config/python.php` | url (http://localhost:8001), secret, timeout, queue |

---

## Lo que falta — Brecha real con el spec

### 🔲 Controllers pendientes (33)

**Organización / Catálogos:**
`OrganizacionController` `EscuelaController` `SedeController` `EdificioController` `AulaController` `CicloEscolarController` `NivelController` `GradoController` `GrupoController` `MateriaController` `PlanEstudioController`

**Académico:**
`HorarioController` `AsistenciaController` `CalificacionController` `PeriodoEvaluacionController` `RegularizacionController`

**Alumnos:**
`BajaController` `TutorController` `TrayectoriaController` `DocenteController`

**Finanzas:**
`ConceptoPagoController` `ParcialidadController`

**Usuarios y seguridad:**
`UserController` `RolController` `PasswordResetController` `TwoFactorController`

**RH:**
`ContratoController` `AsistenciaPersonalController`

**Inventario:**
`ActivoFijoController` `MantenimientoController`

**Comunicación:**
`NotificacionController` `CalendarioController`

**Admisiones:**
`AdmisionController`

**Reportes:**
`ReporteController`

---

### 🔲 Services pendientes (7)

| Service | §Spec | Función |
|---------|-------|---------|
| `NotificacionService` | §85 | Envío multicanal email/SMS/push/interna con segmentación por nivel/grupo/sede |
| `CobranzaService` | §48-§52 | Adeudos, recargos, descuentos, vencimientos |
| `TwoFactorService` | §63 | TOTP o código SMS/email, emitir y verificar |
| `PasswordPolicyService` | §63 | Fuerza, historial, expiración de contraseñas |
| `ConfiguracionHerenciaService` | §87-§88 | Resolución jerárquica Org→Escuela→Sede→Ciclo |
| `ExportService` | §79 | Exportaciones asíncronas con auditoría (quién/qué/filtros/nro.registros) |
| `DocenteService` | §43 | Estadísticas docentes: promedio, aprobación, evolución por ciclo |

---

### 🔲 Vistas funcionales pendientes (≈ 70)

**Organización:** sedes/create+edit, escuelas, edificios, aulas, ciclos  
**Alumnos:** formularios completos create/edit/show, ficha con trayectoria+calificaciones+adeudos, inscripción wizard, baja, reingreso  
**Docentes:** CRUD + asignación grupo-materia  
**Grupos/Materias/Planes:** CRUD completo  
**Horarios:** cuadrícula semanal, formulario con detección colisiones, wizard generación Python  
**Asistencias:** pase de lista interactivo por grupo/fecha  
**Calificaciones:** cuadrícula por periodo/grupo, captura por docente  
**Finanzas:** formularios cargos/pagos completos, desglose parcialidades, estado de caja con movimientos  
**Usuarios y roles:** CRUD con asignación permisos  
**RH:** contratos, pase asistencia personal  
**Inventario:** formularios completos, activos fijos  
**Admisiones:** formulario prospecto, ficha con seguimientos, conversión a alumno  
**Comunicación:** envío notificaciones con segmentación, calendario por sede  
**Documentos:** formulario generación, preview+descarga signed URL  
**Reportes:** disparar job Python, estado, descarga  
**Dashboard enriquecido:** widgets por rol, dashboard de riesgo académico (§73-§75)  

---

### 🔲 Componentes UI pendientes (11)

`x-ui.table` `x-ui.modal` `x-ui.confirm` `x-ui.stat-card` `x-ui.chart`  
`x-ui.breadcrumb` `x-ui.tabs` `x-ui.file-upload` `x-ui.avatar`  
`x-ui.loading` `x-ui.date-picker`

---

### 🔲 Infraestructura Python (directorio `python/` no existe)

| Componente | §Spec |
|-----------|-------|
| `python/app/main.py` — FastAPI app | §3 |
| Worker `calcular_indicadores` | §76 |
| Worker `calcular_riesgo` | §76, §109 |
| Worker `analizar_reprobacion` / `analizar_desercion` | §76 |
| Worker `importaciones` (Excel/CSV pandas) | §77 |
| Worker `generar_horarios` (algoritmo optimización) | §38 |
| Worker `generar_reportes` (PDF/Excel grandes) | §78 |
| Worker `geoprocesamiento` | §54, §56 |
| `python/requirements.txt` | §3 |
| `python/tests/` | §99 |
| Integración Laravel Queue → Python → resultado | §3, §38 |

---

### 🔲 Features de negocio faltantes

| Feature | §Spec |
|---------|-------|
| Flujo recuperación de contraseña | §63 |
| Flujo 2FA completo | §63 |
| Descarga documentos con signed URL + audit | §80 |
| Importaciones masivas Excel/CSV (UI + Job + Python) | §77 |
| Reportes masivos asincrónicos | §78 |
| Auditoría de exportaciones (§79) | §79 |
| Menú dinámico construido desde permisos | §72 |
| Dashboard por rol (superadmin/directivo/docente/cajero) | §73 |
| Modo oscuro light/dark/system | §12 |
| Configuración institucional jerárquica (escala calificación, mínimo aprobatorio, tolerancia retardo) | §87-§88 |
| API REST completa `/api/v1/*` por módulo | §4, §89 |
| Testing suite (Unit + Feature + Authorization) | §97-§99 |
| Backups diarios con retención | §96 |
| Integraciones: SMTP real, SMS, pasarelas pago, LDAP, OAuth | §89 |

---

## Verificaciones del build actual

```
php artisan migrate --force     ✅ 34 migraciones OK
php artisan db:seed --force      ✅ RolesPermisos + Organizacion + Superadmin + Materias
npm run build                    ✅ ~237KB CSS · ~138KB JS
php artisan view:cache           ✅ Blade templates cached
php artisan route:list           ✅ 50 rutas sin errores
Tests RBAC manuales (9/9)        ✅ superadmin/docente/cajero verificados
```

---

*Spec de referencia: `sistema_escolar_laravel_blade_livewire_python_v3.md`*  
*Última verificación real: 2026-09-04*
