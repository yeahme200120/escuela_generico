# 📚 Sistema Escolar Multisede — Estado de Implementación
## Bitácora técnica de desarrollo activo
### Laravel 12 + Bootstrap 5 + Blade + MySQL

**Versión doc:** 6.0 | **Actualizado:** 2026-09-06
**Estado:** Backend Fase 1–8 completo · Frontend parcial · Python pendiente

---

## Conteo real verificado (2026-09-06)

| Artefacto | Cantidad | Estado |
|-----------|---------|--------|
| Migraciones ejecutadas | 44 | ✅ 0 pendientes |
| Tablas MySQL | 60+ | ✅ |
| Modelos Eloquent | 69 | ✅ |
| Controllers | 52 | ✅ todos con lógica real |
| Services | 24 | ✅ namespaces correctos |
| Form Requests | 36 | ✅ |
| Policies | 13 | ✅ |
| Middleware | 6 | ✅ |
| Jobs | 1 | ✅ DispatchPythonJob |
| Seeders | 5 | ✅ ejecutados |
| Rutas registradas | ~290 | ✅ sin errores fatales |
| Vistas funcionales (≥30 ln) | 25 | ✅ |
| Vistas stub (<30 ln) | 176 | ⚠️ muestran "en construcción" |
| Componentes UI Blade | 17 | ✅ todos funcionales |
| Build assets | 237KB CSS + 137KB JS | ✅ |

---

## Stack tecnológico definitivo

| Capa | Tecnología | Versión | Estado |
|------|-----------|---------|--------|
| Backend | Laravel | 12.69.1 | ✅ |
| PHP | PHP | 8.2.12 | ✅ |
| Base de datos | MySQL | 8.x XAMPP | ✅ |
| Autenticación | Sanctum | 4.3 | ✅ |
| Frontend CSS | Bootstrap | 5.3.8 | ✅ compilado Vite |
| Frontend JS | Bootstrap JS + Vanilla | 5.3.8 | ✅ |
| Frontend HTML | Blade | nativo | ✅ |
| Build | Vite + laravel-vite-plugin | 6.x / 1.2 | ✅ |
| RBAC | Custom (sin paquete) | — | ✅ |
| Queue | Database driver | — | ✅ |
| Python / FastAPI | — | 3.12+ | 🔲 pendiente |
| Redis | — | — | 🔲 pendiente producción |

> **Sin Livewire. Sin Alpine.js. Sin Tailwind.**

---

## Errores corregidos acumulados

| Error | Causa | Solución |
|-------|-------|----------|
| `$this->middleware()` en constructores | No existe en Laravel 12 | Eliminado de 9 controllers |
| `$slot` undefined en layouts | 151+ vistas con `@extends` | Convertidas a `<x-layouts.app>` |
| BOM UTF-8 en archivos PHP | Set-Content en PowerShell | `WriteAllBytes` en todo `app/` |
| Namespace `App\Services\PythonJobService` | Servicio en raíz | Corregido a subcarpeta `Python\` |
| Namespace `App\Services\RiesgoAcademicoService` | Idem | Corregido a `Academico\` |
| Namespace `App\Services\DocenteService` | Servicio en raíz | Movido a `Academico\` |
| Namespace `App\Services\NotificacionService` | Idem | Movido a `Comunicacion\` |
| Namespace `App\Services\TwoFactorService` | Idem | Movido a `Seguridad\` |
| `maatwebsite/excel` no instalado | Dependencia no declarada | Reemplazado por CSV nativo |
| Modelo `Reporte` inexistente | Controller lo referenciaba | Creado sobre tabla `python_jobs` |
| Vistas `ciclos_escolares.*` faltantes | Controller apuntaba mal | Creadas 8 vistas completas |
| Migración duplicada `grupos` | Generada por seeder | Eliminada |
| Services duplicados en raíz | Generados externamente | Eliminados, versión en subcarpeta |
| `storage:link` faltante | Nunca ejecutado | Ejecutado |

---

## Base de datos — 44 migraciones · 60+ tablas ✅

| Grupo | Tablas principales |
|-------|-------------------|
| Infraestructura | cache, jobs, sessions, personal_access_tokens |
| Organización | organizaciones, escuelas, sedes, edificios, aulas |
| Académico | ciclos_escolares, niveles_educativos, grados, grupos, materias, planes_estudio, plan_materias |
| Usuarios RBAC | users, roles, permissions, role_permissions, user_roles, user_permissions, user_sedes, system_settings |
| Trazabilidad | user_sessions, access_logs, audit_logs, query_logs |
| Alumnos | alumnos, tutores, alumno_tutor, docentes, docente_grupo_materia, trayectorias_alumno, alumno_grupo_historial, bajas, reingresos |
| Operación académica | horario_bloques, horarios, periodos_asistencia, asistencias, justificantes, periodos_evaluacion, calificaciones, regularizaciones |
| Control escolar | tipos_documento, documentos, folios |
| Finanzas | conceptos_pago, cargos, parcialidades, metodos_pago, pagos, pago_detalle |
| Caja | cajas, turnos_caja, movimientos_caja |
| RH | empleados, contratos, asistencia_personal |
| Inventario | categorias_inventario, inventario, movimientos_inventario, activos_fijos |
| Comunicación | notificaciones, notificacion_usuario, calendario_escolar |
| Admisiones | prospectos, seguimientos_prospecto, admisiones |
| Extras | mantenimiento, reingresos, webhook_events, python_jobs |

---

## Controllers — 52 archivos con lógica real

### Auth (2)
`LoginController` — GET/POST login con captura geo+device en campos hidden  
`LogoutController` — POST logout con revocación de UserSession

### Auditoría (4)
`AuditLogController` — filtros completos + modal before/after JSON  
`AccessLogController` — badges de anomalías geo  
`SesionesController` — revocar sesiones activas  
`QueryLogController` — SQL expandible + queries lentas

### Configuración (1)
`AparienciaController` — color pickers + ThemeService + cache

### Organización/Catálogos (8)
`OrganizacionController`, `EscuelaController`, `SedeController`  
`EdificioController`, `AulaController`  
`CicloEscolarController`, `NivelController`, `GradoController`

### Académico (9)
`GrupoController`, `MateriaController`, `PlanEstudioController`  
`HorarioController` — con `HorarioConflictService` §37  
`AsistenciaController` — pase de lista + justificantes §39  
`CalificacionController` — cuadrícula + lock periodo cerrado §41  
`PeriodoEvaluacionController` — cerrar periodo §41  
`RegularizacionController` §28  
`TrayectoriaController` §22

### Alumnos (5)
`AlumnoController` — CRUD + authorize + audit  
`InscripcionController` — DB::transaction trayectoria+historial §22  
`TutorController` — CRUD + vinculación alumno-tutor §20  
`BajaController` — BajaService + procesarReingreso §24-§27  
`DocenteController` — CRUD + asignación grupo-materia §35

### Finanzas (5)
`CargoController` — cálculo total + cancelación §48  
`PagoController` — PagoService idempotencia §50  
`CajaController` — CajaService apertura/cierre/movimientos §51  
`ConceptoPagoController`, `ParcialidadController`

### RH (3)
`EmpleadoController`, `ContratoController`, `AsistenciaPersonalController`

### Inventario (2)
`InventarioController` — movimientos stock §81  
`ActivoFijoController` §82

### Control Escolar (2)
`DocumentoController` — DocumentoService + folios §46  
`MantenimientoController` §83

### Admisiones (2)
`ProspectoController` — CRM + seguimientos §84  
`AdmisionController` §84

### Comunicación (2)
`NotificacionController` — NotificacionService §85  
`CalendarioController` §86

### Seguridad (2)
`PasswordResetController` — flujo completo §63  
`TwoFactorController` — activar/verificar/desactivar §63

### Reportes (1)
`ReporteController` — CSV nativo + PythonJobService §78

### Usuarios RBAC (2)
`UserController` — CRUD + asignación roles/sedes §14  
`RolController` — CRUD + asignación permisos §17

### Exportación (1)
`ExportController` — CSV nativo auditado §79

---

## Services — 24 archivos con namespaces correctos

| Service | Namespace | Función principal |
|---------|-----------|------------------|
| `AuditService` | `Auditoria\` | log(), logModel(), logAccess(), detección anomalías |
| `QueryLogger` | `Auditoria\` | DB::listen, buffer+flush, queries lentas |
| `AuthService` | `Auth\` | login+logout con geo, UserSession, rate limiting |
| `LoginResult` | `Auth\` | Value object: success/failed/blocked |
| `ThemeService` | `Configuracion\` | getForOrganizacion() cache 30min |
| `PagoService` | `Finanzas\` | registrar() idempotencia SHA-256, cancelar() |
| `CajaService` | `Finanzas\` | abrir(), cerrar() monto_esperado, registrarMovimiento() |
| `DocumentoService` | `Documentos\` | generarFolio() lockForUpdate, crear(), autorizar() |
| `AsistenciaService` | `Academico\` | registrarLista(), aplicarJustificante() §39-§40 |
| `CalificacionService` | `Academico\` | registrar(), cerrarPeriodo() §41 |
| `HorarioConflictService` | `Academico\` | 5 tipos de colisión §37 |
| `RiesgoAcademicoService` | `Academico\` | motor reglas 4 niveles + calcularMasivo() §29 |
| `IndicadoresService` | `Academico\` | % aprobación/deserción/permanencia §30 |
| `DocenteService` | `Academico\` | estadísticas docente §43 |
| `BajaService` | `Alumnos\` | registrarBaja(), procesarReingreso() §24-§27 |
| `PythonJobService` | `Python\` | despachar(), ejecutar() HTTP FastAPI |
| `WebhookService` | `Webhook\` | HMAC, idempotencia, reintentos §90 |
| `TwoFactorService` | `Seguridad\` | TOTP, activar/verificar/desactivar §63 |
| `NotificacionService` | `Comunicacion\` | envío multicanal §85 |
| `CobranzaService` | raíz | adeudos, recargos §48-§52 |
| `GeneradorReportesService` | raíz | CSV nativo §78 |
| `ExportService` | raíz | CSV auditado §79 |
| `ConfiguracionHerenciaService` | raíz | resolución Org→Sede→Ciclo §87-§88 |
| `PasswordPolicyService` | raíz | fuerza, historial, expiración §63 |

---

## Form Requests — 36 archivos

### Auth (1): `LoginRequest`
### Admin (5): `UserStore/UpdateRequest`, `RolRequest`, `OrganizacionRequest`, `EmpleadoRequest`, `DocumentoRequest`, `InventarioRequest`
### Académico (9): `AlumnoStore/UpdateRequest`, `CicloEscolarStore/UpdateRequest`, `EscuelaStore/UpdateRequest`, `GradoStore/UpdateRequest`, `GrupoStore/UpdateRequest`, `MateriaStore/UpdateRequest`, `NivelEducativoStore/UpdateRequest`, `SedeStore/UpdateRequest`, `InscripcionRequest`, `AsistenciaRequest`, `CalificacionRequest`, `HorarioRequest`, `BajaRequest`, `DocenteRequest`, `TutorRequest`, `RegularizacionRequest`, `PeriodoEvaluacionRequest`, `PlanEstudioRequest`
### Finanzas (2): `PagoRequest`, `CargoRequest`

---

## Policies — 13 archivos

| Policy | Recursos que protege |
|--------|---------------------|
| `BasePolicy` | `before()`: superadmin pasa, inactivo/bloqueado deniega |
| `UserPolicy` | Jerarquía de niveles, misma org, no autoeliminarse |
| `AuditoriaPolicy` | Inmutable: create/update/delete = false siempre |
| `SystemSettingPolicy` | theme.* solo con permiso `configuracion.apariencia.editar` |
| `SedePolicy` | Scope por org + userSedes, no eliminar físicamente |
| `AlumnoPolicy` | viewAny/view/create/update/delete/export por permiso |
| `GrupoPolicy` | Docente solo ve sus grupos, grupos no se eliminan |
| `MateriaPolicy` | Scope por escuela de la organización |
| `CicloEscolarPolicy` | Ciclos no se eliminan físicamente |
| `EscuelaPolicy` | Scope por organización |
| `GradoPolicy` | Scope por nivel educativo |
| `NivelEducativoPolicy` | Scope por escuela |
| `EstudiantePolicy` | Alias de AlumnoPolicy |

---

## Middleware — 6 archivos

| Middleware | Función | §Spec |
|-----------|---------|-------|
| `SetRequestId` | Genera `REQ-{ULID}` → RequestContext + header respuesta | §61 |
| `GeoTrace` | Construye GeoContext, inicia QueryLogger, actualiza last_seen_at | §54-§56 |
| `CheckUserActive` | Verifica activo=true y no bloqueado | §63 |
| `EnsureTwoFactor` | Redirige a challenge si 2FA activo y no verificado | §63 |
| `EnsureOrganizacion` | Bloquea usuarios sin organización asignada | §13 |
| `ScopeToOrganizacion` | Inyecta _org_id, bloquea cross-org | §18 |

---

## Componentes UI — 17 (todos funcionales)

`x-ui.alert` · `x-ui.avatar` · `x-ui.badge` · `x-ui.breadcrumb` · `x-ui.card`  
`x-ui.chart` (requiere Chart.js CDN) · `x-ui.confirm` · `x-ui.date-picker`  
`x-ui.empty-state` · `x-ui.file-upload` · `x-ui.filter-bar` · `x-ui.loading`  
`x-ui.modal` · `x-ui.page-header` · `x-ui.stat-card` · `x-ui.table` · `x-ui.tabs`

---

## Vistas funcionales completas (25)

`auth/login` · `auditoria/index` · `auditoria/accesos` · `auditoria/sesiones` · `auditoria/queries`  
`configuracion/apariencia` · `dashboard` · `ciclos_escolares/` (4 vistas) · `niveles_educativos/` (4 vistas)  
`alumnos/index` · `alumnos/create` · `alumnos/edit` · `horarios/index` · `horarios/create`  
`calificaciones/index` · `grupos/index` · `grupos/create` · `grupos/edit` · `materias/index`

---

## Vistas stub (176) — Sin errores, muestran "en construcción"

```
activos-fijos/ admisiones/ asistencia-personal/ asistencias/ aulas/
bajas/ calendario/ calificaciones/(create/edit/show) ciclos/ conceptos/
contratos/ docentes/ documentos/ edificios/ emails/ escuelas/ finanzas/
grados/ grupos/show inventario/ mantenimientos/ materias/(create/edit/show)
niveles/ notificaciones/ organizaciones/ parcialidades/ password-resets/
periodos-evaluacion/ planes/ regularizaciones/ reportes/ rh/empleados/
roles/ sedes/ trayectorias/ tutores/ two-factor/ users/
```

---

## Seeders ejecutados ✅

| Seeder | Datos |
|--------|-------|
| `RolesPermisosSeeder` | 11 roles + 53 permisos + asignación |
| `OrganizacionSeeder` | Org demo + Escuela + Sede Norte + 4 aulas + ciclo 2026-2027 |
| `SuperadminSeeder` | superadmin/directivo/docente/cajero · pass: `Admin@2026!` |
| `MateriaSeeder` | 10 materias base |

---

## Trazabilidad — Implementación completa §54-§62 §100

**Pipeline:** SetRequestId → GeoTrace → CheckUserActive → Controller → QueryLogger.flush()

**Captura en cada request:**
- `X-Geo-Latitude/Longitude/Accuracy` → lat/lon/precisión en audit_logs
- `X-Device-ID` → SHA-256 fingerprint del dispositivo
- `X-Device-Info` → JSON pantalla/timezone/platform
- `X-Request-ID` → REQ-{ULID} propagado a audit_logs y query_logs

**Anomalías detectadas:** nuevo_dispositivo · nueva_ubicacion · fuera_geocerca · fuera_de_horario · viaje_imposible (Haversine >900 km/h)

---

## Lo que falta — Pendiente real

### P1 — Crítico para uso del sistema
| # | Qué falta | Archivo(s) a crear |
|---|-----------|-------------------|
| 1 | Vista alumnos/show completa (trayectoria+calificaciones+adeudos) | `resources/views/alumnos/show.blade.php` |
| 2 | Vista calificaciones/index con cuadrícula real | `resources/views/calificaciones/index.blade.php` |
| 3 | Vista asistencias/index con pase de lista | `resources/views/asistencias/index.blade.php` |
| 4 | Vista users/index y users/create | `resources/views/users/` |
| 5 | Vista finanzas/cargos/index y create completa | `resources/views/finanzas/cargos/` |
| 6 | Vista finanzas/pagos/index y create completa | `resources/views/finanzas/pagos/` |
| 7 | Vista finanzas/caja/index con turno activo | `resources/views/finanzas/caja/` |
| 8 | Dashboard enriquecido por rol con indicadores reales | `resources/views/dashboard.blade.php` |

### P2 — Operación académica
| # | Qué falta |
|---|-----------|
| 9 | Vistas docentes CRUD (create/edit/show) |
| 10 | Vistas grupos (show con alumnos y horario) |
| 11 | Vistas organizaciones/escuelas/sedes CRUD |
| 12 | Vistas horarios (cuadrícula publicada) |
| 13 | Vista bajas/create con tipos temporal/definitiva |

### P3 — Módulos avanzados
| # | Qué falta |
|---|-----------|
| 14 | Infraestructura Python (`python/` directorio + FastAPI + workers) |
| 15 | API REST completa `/api/v1/*` con Sanctum |
| 16 | Vistas 2FA (challenge, activar, códigos recuperación) |
| 17 | Vistas password-reset (forgot-password, reset-password) |
| 18 | Tests (Unit + Feature + Authorization) |
| 19 | Notificaciones multicanal UI completa |
| 20 | Importaciones masivas Excel/CSV (UI + Job + Python) |

---

## Verificaciones actuales ✅

```
php artisan about              ✅ Laravel 12.69.1 / PHP 8.2.12
php artisan route:list         ✅ ~290 rutas, 0 errores fatales
php artisan view:cache         ✅ Blade templates cached
npm run build                  ✅ 237KB CSS + 137KB JS
php artisan storage:link       ✅ vinculado
BOM UTF-8 en PHP               ✅ 0 archivos afectados
@extends en vistas             ✅ 0 vistas con @extends
Clases faltantes               ✅ 0 referencias rotas
Servicios duplicados           ✅ 0 duplicados
Namespaces incorrectos         ✅ todos corregidos
```
