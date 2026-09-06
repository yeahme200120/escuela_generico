# 📋 Sistema Escolar — TODO: Todo lo Pendiente del Sistema
## Brecha completa entre spec §1-§115 y estado real implementado
### Generado: 2026-09-06 | Base: sistema_escolar_laravel_blade_livewire_python_v3.md

---

## Estado actual verificado antes de este documento

```
Models:             71   Controllers: 52   Services: 24
Requests:           36   Policies:    13   Middleware: 6
Migrations:         44   Routes:     307   Tests: 4
Vistas completas:  127   Stubs:       77   UI components: 17
Python workers:      5 (estructuras vacías)
API REST:            1 endpoint (/api/v1/user)
```

---

## RESUMEN EJECUTIVO

| Categoría | Items | Complejidad |
|-----------|-------|-------------|
| A) Vistas stub con lógica incompleta | 30 | ALTA/MEDIA |
| B) Vistas inexistentes requeridas por spec | 27 | ALTA |
| C) Features de negocio incompletas | 16 | ALTA |
| D) Python workers con datos reales | 9 | ALTA |
| E) API REST /api/v1/ completa | 18 | ALTA |
| F) Testing §97-§99 | 16 | ALTA/MEDIA |
| G) Integraciones externas §89-§90 | 9 | ALTA |
| H) Configuración y seguridad | 14 | ALTA/MEDIA |
| I) Dashboard enriquecido por rol §70-§75 | 9 | ALTA |
| **TOTAL** | **~148** | |

---

## A) VISTAS STUB QUE NECESITAN LÓGICA COMPLETA

> Estas vistas **existen** pero muestran `{{ $slot ?? '' }}` vacío o `toArray()` genérico.

### A1 — Formularios sin campos reales (patrones vacíos)

| # | Vista | §Spec | Qué implementar | Complejidad |
|---|-------|-------|-----------------|-------------|
| A1.1 | `horarios/create.blade.php` | §36-§37 | Selector grupo/materia/docente/aula/día/hora + validación visual de colisiones contra `HorarioConflictService` | ALTA |
| A1.2 | `asistencias/create.blade.php` | §39 | Pase de lista: tabla grupo con radio presente/falta/retardo/justificada + registro en lote | ALTA |
| A1.3 | `calificaciones/create.blade.php` | §41 | Captura alumno+materia+periodo, calificación numérica, resultado automático, alerta periodo cerrado | ALTA |
| A1.4 | `regularizaciones/create.blade.php` | §28 | Selector alumno+materia+ciclo, calificación original, calificación regularización, fecha, resultado | MEDIA |
| A1.5 | `trayectorias/create.blade.php` | §22 | Selector ciclo/sede/grado/grupo, estatus, situación académica, fecha inicio, motivo | MEDIA |
| A1.6 | `planes/create.blade.php` | §33 | Nombre/clave/nivel + tabla de materias por grado (plan_materias), obligatoria/optativa | ALTA |
| A1.7 | `activos-fijos/create.blade.php` | §82 | código, número_serie, categoría, sede, edificio, aula, responsable, valor, estado, fecha_adquisicion | MEDIA |
| A1.8 | `admisiones/create.blade.php` | §84 | prospecto vinculado, sede, ciclo, grado, evaluación, fecha_solicitud, fecha_resolucion, estatus | MEDIA |
| A1.9 | `notificaciones/create.blade.php` | §85 | título, cuerpo, canal (email/SMS/push/interna), segmentación por sede/nivel/grado/grupo | ALTA |
| A1.10 | `alumnos/inscripcion/create.blade.php` | §44 | Wizard: alumno→ciclo→sede→grado→grupo, verificación de cupo, generación de trayectoria | ALTA |
| A1.11 | `contratos/create.blade.php` | §53 | empleado, tipo contrato, fecha_inicio, fecha_fin, salario, documento, activo | MEDIA |
| A1.12 | `grupos/create.blade.php` | §31 | sede, ciclo, grado, nombre, turno, capacidad, aula_principal, docente_tutor | MEDIA |
| A1.13 | `grupos/edit.blade.php` | §31 | Mismo que create + aviso si hay alumnos inscritos | MEDIA |
| A1.14 | `materias/create.blade.php` | §32 | clave, nombre, tipo (enum), horas_semana, créditos, descripción | BAJA |
| A1.15 | `materias/edit.blade.php` | §32 | Idem + aviso si está en planes activos | BAJA |
| A1.16 | `rh/empleados/create.blade.php` | §53 | user_id (select), número_empleado, puesto, departamento, tipo_contrato, salario, fecha_ingreso | MEDIA |
| A1.17 | `rh/empleados/edit.blade.php` | §53 | Mismo que create + estatus (activo/baja/suspendido) | MEDIA |
| A1.18 | `calendario/create.blade.php` | §86 | título, tipo (enum 8 valores), fecha_inicio, fecha_fin, sede_id, ciclo_id, color, descripción | MEDIA |
| A1.19 | `docentes/create.blade.php` | §34 | user_id, número_empleado, especialidad, cédula, tipo_contrato, fecha_ingreso, estatus | MEDIA |
| A1.20 | `docentes/edit.blade.php` | §34 | Mismo que create + actualización de estatus | MEDIA |

### A2 — Show genéricos (usan `toArray()` — sin estructura semántica)

| # | Vista | §Spec | Qué implementar | Complejidad |
|---|-------|-------|-----------------|-------------|
| A2.1 | `grupos/show.blade.php` | §31 | Ficha grupo: tabs alumnos/horario/docentes/estadísticas asistencia+calificaciones | ALTA |
| A2.2 | `trayectorias/show.blade.php` | §22 | Timeline cronológico: ciclos, cambios de sede/grupo/estado, historial completo | ALTA |
| A2.3 | `docentes/show.blade.php` | §34 §43 | Ficha docente: datos + grupos asignados + materias + estadísticas por grupo/materia/ciclo | ALTA |
| A2.4 | `asistencias/show.blade.php` | §39-§40 | Detalle asistencia individual + estado justificante vinculado | MEDIA |
| A2.5 | `calificaciones/edit.blade.php` | §41 | Formulario edición + permiso especial periodo cerrado + reason obligatorio + before/after en audit | ALTA |
| A2.6 | `bajas/show.blade.php` | §24-§26 | Tipo baja, documentos, autorizaciones, botón proceso reingreso si aplica | MEDIA |
| A2.7 | `bajas/edit.blade.php` | §24 | Cambio estatus (aprobada/activa/cancelada), usuario_autoriza, observaciones | MEDIA |
| A2.8 | `reportes/show.blade.php` | §78-§79 | Progreso del job Python, descarga Signed URL, auditoría de exportación | ALTA |
| A2.9 | `calendario/show.blade.php` | §86 | Detalle evento + acciones editar/cancelar + breadcrumb sede/ciclo | BAJA |
| A2.10 | `activos-fijos/show.blade.php` | §82 | Ficha activo: datos + historial transferencias entre sedes + mantenimientos asociados | MEDIA |

---

## B) VISTAS INEXISTENTES (spec las requiere, no existen en absoluto)

| # | Vista a crear | §Spec | Qué implementar | Complejidad |
|---|--------------|-------|-----------------|-------------|
| B1 | `documentos/create.blade.php` | §45-§46 | Modal/formulario generar documento: tipo, alumno, generación folio automático | ALTA |
| B2 | `documentos/show.blade.php` | §46 §80 | Preview + descarga Signed URL + historial auditoría + autorización | ALTA |
| B3 | `finanzas/pagos/create.blade.php` | §50 | Formulario: alumno, cargos pendientes (multi-concepto), método pago, caja activa, idempotencia | ALTA |
| B4 | `finanzas/pagos/show.blade.php` | §50 §52 | Recibo + cargos aplicados + botones cancelar/devolver con motivo obligatorio | ALTA |
| B5 | `finanzas/cargos/create.blade.php` | §48 | Cargo manual: alumno, concepto, importe, descuento, recargo, vencimiento | MEDIA |
| B6 | `finanzas/cargos/show.blade.php` | §48-§49 | Detalle cargo + parcialidades + historial pagos aplicados | MEDIA |
| B7 | `finanzas/caja/turno.blade.php` | §51 | Vista turno activo: movimientos, totales por método, botones ingreso/egreso/retiro/arqueo | ALTA |
| B8 | `finanzas/adeudos.blade.php` | §48-§50 | Lista alumnos con adeudos, filtros sede/ciclo/estado, acciones cobranza, aging | ALTA |
| B9 | `alumnos/historial-academico.blade.php` | §42 | Historia completa: grupos, calificaciones, periodos — filtros ciclo/materia | ALTA |
| B10 | `alumnos/kardex.blade.php` | §45 | Kardex oficial: calificaciones por ciclo y materia, formateado para impresión | ALTA |
| B11 | `alumnos/boleta.blade.php` | §45 §106 | Boleta por periodo: calificaciones + promedios + observaciones, generación PDF | ALTA |
| B12 | `calificaciones/actas.blade.php` | §44 §106 | Actas de calificaciones cerradas por grupo/periodo | ALTA |
| B13 | `importar/alumnos.blade.php` | §77 | Upload Excel → previsualizar filas → confirmar → encolar Job Python | ALTA |
| B14 | `importar/calificaciones.blade.php` | §77 | Mismo flujo para importación masiva de calificaciones | ALTA |
| B15 | `configuracion/herencia.blade.php` | §87-§88 | UI configuración jerárquica Org→Escuela→Sede→Ciclo con fallback visual | ALTA |
| B16 | `configuracion/geocerca.blade.php` | §56 §87 | Configurar lat/lon/radio por sede (inputs numéricos) con visualización | MEDIA |
| B17 | `configuracion/politicas.blade.php` | §63 §87 | Escala calificaciones, mínimo aprobatorio, periodos, tolerancia retardo, moneda | MEDIA |
| B18 | `configuracion/modo-oscuro.blade.php` | §12 | Toggle modo oscuro por organización (superadmin) + preferencia usuario | BAJA |
| B19 | `admisiones/seguimientos.blade.php` | §84 | Kanban/lista de seguimientos CRM de prospectos, citas, evaluaciones | ALTA |
| B20 | `admisiones/prospectos/create.blade.php` | §84 | Formulario prospecto + primera nota de seguimiento + sede de interés | MEDIA |
| B21 | `asistencias/justificantes.blade.php` | §40 | Lista + flujo solicitud/aprobación justificantes, sin borrar asistencia original | ALTA |
| B22 | `riesgo/dashboard.blade.php` | §29 §75 | Dashboard riesgo: distribución 4 niveles + tabla alumnos + materias críticas + tendencia | ALTA |
| B23 | `docentes/estadisticas.blade.php` | §43 | Indicadores por docente: promedio, aprobación, reprobación, asistencia, comparativa grupos/ciclos | ALTA |
| B24 | `grupos/asignacion-docentes.blade.php` | §35 | CRUD de docente_grupo_materia con tabla editable por grupo/ciclo | ALTA |
| B25 | `exportar/index.blade.php` | §79 | Historial exportaciones auditado: quién, qué, cuándo, filtros, nº registros, descarga | MEDIA |
| B26 | `python/jobs.blade.php` | §76-§78 | Monitor jobs Python: estado, progreso barra, resultado, reintentar, errores | MEDIA |
| B27 | `estadisticas/academicas.blade.php` | §74 | Dashboard estadísticas: Promedio/Aprobación/Reprobación/Asistencia/Deserción por filtros | ALTA |

---

## C) FEATURES DE NEGOCIO INCOMPLETAS EN SERVICES/CONTROLLERS

| # | Feature | §Spec | Qué falta | Complejidad |
|---|---------|-------|-----------|-------------|
| C1 | **CobranzaService** — lógica real | §48-§50 | Vencer cargos automáticamente, calcular adeudos totales, aging de cartera (30/60/90 días), alertas de vencimiento próximo | ALTA |
| C2 | **GeneradorReportesService** — auditoría | §78-§79 | Registrar en audit_logs: quién exportó, qué, filtros, nº registros, archivo, Request ID + Signed URL de descarga | ALTA |
| C3 | **NotificacionService** — SMS y Push | §85 | Solo email e interna. Faltan canales SMS (Twilio/Nexmo) y Push (FCM/APNs) | ALTA |
| C4 | **NotificacionService** — segmentación | §85 | Envío por Org/Escuela/Sede/Nivel/Grado/Grupo/Rol | ALTA |
| C5 | **ConfiguracionHerenciaService** — resolución | §87-§88 | Fallback completo Org→Escuela→Sede→Ciclo. La más específica tiene prioridad | MEDIA |
| C6 | **DocumentoService** — Signed URLs + PDF | §46 §80 | Storage privado + URL firmada temporal + generación PDF vía Python | ALTA |
| C7 | **RiesgoAcademicoService** — dispatch masivo | §29 §76 | `calcularMasivo()` debe serializar datos y despachar a Python worker `riesgo.py` | ALTA |
| C8 | **IndicadoresService** — integración Python | §30 §74 | La versión masiva/histórica debe encolarse a `estadisticas.py` (actualmente PHP puro) | ALTA |
| C9 | **HorarioConflictService** — endpoint AJAX | §37 | No hay endpoint para validación de colisiones en tiempo real desde el formulario | MEDIA |
| C10 | **AsistenciaService** — validación geocerca | §56 | Registro de asistencia laboral debe validar que coordenadas estén dentro del radio de la sede | MEDIA |
| C11 | **PagoService** — devoluciones `devolverPago()` | §50 §52 | Flujo completo: reversión cargos + movimiento caja + auditoría + permiso `pagos.devolver` | ALTA |
| C12 | **CajaService** — arqueo/ajuste | §51 §52 | `ajustarCaja()` con motivo obligatorio + lock + auditoría. Solo existe apertura/cierre | MEDIA |
| C13 | **ExportService** — registro auditoría | §79 | No registra en audit_logs el acto de exportación (quién, qué, filtros, nº registros, archivo) | MEDIA |
| C14 | **PasswordPolicyService** — conexión real | §63 | El service existe pero NO está conectado a `UserController` ni `PasswordResetController` | MEDIA |
| C15 | **SuspiciousAccessService** — detección | §64 | No existe. Debe detectar: múltiples fallos, nuevo device, cambio geográfico abrupto, distancia imposible | ALTA |
| C16 | **InscripcionController** — lockForUpdate | §93 | Usa `DB::transaction()` pero sin `lockForUpdate()` en verificación de cupo del grupo | MEDIA |

---

## D) PYTHON WORKERS CON LÓGICA REAL

> Estructura `python/` existe. Todos los workers son stubs con `# TODO`.

| # | Worker | §Spec | Qué implementar | Complejidad |
|---|--------|-------|-----------------|-------------|
| D1 | `estadisticas.py` — lógica real | §76 §74 | Conectar MySQL con pymysql+pandas, calcular: approval_rate, failure_rate, dropout_rate, attendance_rate, retention_rate | ALTA |
| D2 | `riesgo.py` — payload desde Laravel | §29 §75 | Definir contrato de datos: Laravel serializa alumno+calificaciones+asistencias antes de despachar | MEDIA |
| D3 | `horarios.py` — algoritmo real | §38 | Implementar backtracking/CSP o algoritmo genético con restricciones: docente, aula, grupo, capacidad, disponibilidad | ALTA |
| D4 | `importaciones.py` — lógica real | §77 | Leer Excel/CSV con openpyxl/pandas, validar fila a fila, generar reporte errores/advertencias, retornar resultado | ALTA |
| D5 | `reportes.py` — generación real | §78 | Generar CSV/XLSX con openpyxl, subir a storage privado, retornar path relativo a Laravel | ALTA |
| D6 | `geoprocesamiento.py` — nuevo worker | §3 §109 | Análisis de ubicaciones de accesos, mapas de calor, detección patrones geográficos sospechosos | ALTA |
| D7 | **Tests Python** (`tests/`) | §99 | Escribir tests con pytest: estadisticas, importaciones, horarios (básicos→completos), riesgo, geoprocesamiento | ALTA |
| D8 | **pymysql en requirements.txt** | §3 | Agregar `PyMySQL==1.1.1` para conexión real a MySQL desde workers | BAJA |
| D9 | **Unificar workers duplicados** | §109 | `calcular_indicadores.py` y `procesar_importaciones.py` parecen duplicados de `estadisticas.py` e `importaciones.py` — unificar o diferenciar | MEDIA |

---

## E) API REST /api/v1/ COMPLETA

> `app/Http/Controllers/Api/V1/` no existe. `app/Http/Resources/` no existe.
> Solo hay `/api/v1/user` en `routes/api.php`.

### E1 — Directorios y estructura base

| # | Qué crear | §Spec | Complejidad |
|---|-----------|-------|-------------|
| E1.1 | `app/Http/Controllers/Api/V1/` directorio + controllers | §4 | ALTA |
| E1.2 | `app/Http/Resources/` directorio + API Resources (transformers) | §4 | ALTA |

### E2 — Endpoints por módulo

| # | Endpoint | §Spec | Complejidad |
|---|----------|-------|-------------|
| E2.1 | `GET /api/v1/alumnos` | §20-§22 | MEDIA |
| E2.2 | `GET /api/v1/alumnos/{id}` | §20 | MEDIA |
| E2.3 | `GET /api/v1/alumnos/{id}/trayectoria` | §22 | MEDIA |
| E2.4 | `GET /api/v1/alumnos/{id}/calificaciones` | §41-§42 | MEDIA |
| E2.5 | `GET /api/v1/alumnos/{id}/asistencias` | §39 | MEDIA |
| E2.6 | `GET /api/v1/grupos/{id}/horario` | §36 | BAJA |
| E2.7 | `GET/POST /api/v1/asistencias` | §39 | MEDIA |
| E2.8 | `GET/POST /api/v1/calificaciones` | §41 | MEDIA |
| E2.9 | `GET /api/v1/pagos` | §50 | MEDIA |
| E2.10 | `GET/POST /api/v1/python/jobs` | §76-§78 | ALTA |
| E2.11 | `GET /api/v1/python/jobs/{jobId}` | §76 | BAJA |
| E2.12 | `GET /api/v1/notificaciones` | §85 §71 | MEDIA |
| E2.13 | `PUT /api/v1/notificaciones/{id}/leer` | §85 | BAJA |
| E2.14 | `GET /api/v1/indicadores` | §30 §74 | ALTA |
| E2.15 | `GET /api/v1/riesgo` | §29 §75 | ALTA |
| E2.16 | `POST /api/v1/webhooks` (receptor externo) | §90 | ALTA |

---

## F) TESTING §97-§99

> Solo existen 4 tests (3 de ejemplo + 1 básico de EstudianteController).

### F1 — Tests unitarios (Unit Tests)

| # | Test | §Spec | Complejidad |
|---|------|-------|-------------|
| F1.1 | `Unit/CalificacionServiceTest.php` | §97 | MEDIA |
| F1.2 | `Unit/RiesgoAcademicoServiceTest.php` | §97 | MEDIA |
| F1.3 | `Unit/IndicadoresServiceTest.php` | §97 | MEDIA |
| F1.4 | `Unit/PagoServiceIdempotenciaTest.php` | §97 §91 | ALTA |
| F1.5 | `Unit/HorarioConflictServiceTest.php` | §97 §37 | MEDIA |
| F1.6 | `Unit/BajaServiceTest.php` | §97 | MEDIA |

### F2 — Tests de feature (Feature Tests)

| # | Test | §Spec | Complejidad |
|---|------|-------|-------------|
| F2.1 | `Feature/Auth/LoginTest.php` | §97 | MEDIA |
| F2.2 | `Feature/Auth/TwoFactorTest.php` | §97 §63 | ALTA |
| F2.3 | `Feature/Alumnos/AlumnoCRUDTest.php` | §97 | MEDIA |
| F2.4 | `Feature/Alumnos/InscripcionTest.php` | §97 §103-§105 | ALTA |
| F2.5 | `Feature/Finanzas/PagoConReversion.php` | §97 §52 | ALTA |
| F2.6 | `Feature/Academico/CierreCalificaciones.php` | §97 | ALTA |

### F3 — Tests de autorización multisede

| # | Test | §Spec | Complejidad |
|---|------|-------|-------------|
| F3.1 | Docente no accede a grupo ajeno | §97 | MEDIA |
| F3.2 | Cajero no modifica calificaciones | §97 | MEDIA |
| F3.3 | Sede A no ve datos de Sede B | §97 | ALTA |
| F3.4 | Alumno no consulta otro alumno | §97 | MEDIA |

### F4 — Tests Python (pytest)

| # | Test | §Spec | Complejidad |
|---|------|-------|-------------|
| F4.1 | `test_estadisticas.py` | §99 | ALTA |
| F4.2 | `test_importaciones.py` | §99 | ALTA |
| F4.3 | `test_horarios.py` | §99 | ALTA |
| F4.4 | `test_riesgo_completo.py` | §99 | MEDIA |
| F4.5 | `test_geoprocesamiento.py` | §99 | ALTA |

---

## G) INTEGRACIONES EXTERNAS §89-§90

| # | Integración | §Spec | Qué falta | Complejidad |
|---|------------|-------|-----------|-------------|
| G1 | **SMTP real + Mailables** | §89 | Templates `NotificacionMail`, `AlertaAccesoMail`, `DocumentoDisponibleMail` con colas | MEDIA |
| G2 | **SMS — proveedor real** | §85 §89 | `NotificacionService::enviarSMS()` conectado a Twilio/Nexmo/proveedor MX | ALTA |
| G3 | **Push notifications** | §85 §89 | Integración FCM/APNs, `NotificacionService::enviarPush()` | ALTA |
| G4 | **Pasarelas de pago** | §89 | Integración Stripe/Conekta/PayPal con idempotencia (§91), `PagoExternoService` | ALTA |
| G5 | **Facturación electrónica** | §89 | CFDI/SAT (México) o equivalente, `FacturacionService` | ALTA |
| G6 | **LDAP/Active Directory** | §89 | `LdapAuthService`, login federado en `AuthService` | ALTA |
| G7 | **OAuth/SSO** | §89 | Laravel Socialite, providers Google/Microsoft, rutas `/auth/{provider}` | ALTA |
| G8 | **Webhooks — rutas receptoras** | §90 | `POST /api/v1/webhooks` + `WebhookController` (el `WebhookService` con HMAC ya existe) | ALTA |
| G9 | **Moodle/Canvas LMS** | §89 | Sincronización alumnos, grupos, calificaciones con LMS externos | ALTA |

---

## H) CONFIGURACIÓN Y SEGURIDAD §63 §87-§88 §93-§96

| # | Feature | §Spec | Qué falta | Complejidad |
|---|---------|-------|-----------|-------------|
| H1 | **Bloqueo temporal de cuentas** | §63 | `AuthService` incrementa `intentos_fallidos` pero no bloquea automáticamente tras N intentos | MEDIA |
| H2 | **Alertas de seguridad** | §63 §64 | `SuspiciousAccessService`: detectar nuevo dispositivo, viaje imposible, múltiples fallos → notificar al usuario | ALTA |
| H3 | **Políticas de contraseña aplicadas** | §63 | `PasswordPolicyService` existe pero no está wired en `UserController`/`PasswordResetController` | MEDIA |
| H4 | **Control de dispositivos** | §63 | Vista "mis dispositivos" con botón "cerrar todos excepto este" | MEDIA |
| H5 | **Configuración institucional completa** | §87 | Solo tema en `system_settings`. Falta: escala calificación, mínimo aprobatorio, periodos, tolerancia retardo, moneda, retención de datos geo | ALTA |
| H6 | **Herencia de configuración** | §88 | UI configuración por nivel (Org→Escuela→Sede→Ciclo) con resolución de prioridad | ALTA |
| H7 | **Geocercas configurables** | §56 | `Sede` tiene campos `latitud/longitud/radio_geocerca_metros` pero no hay UI de configuración ni validación en asistencia laboral | MEDIA |
| H8 | **Logs separados por canal** | §94 | `logging.php` solo tiene el estándar. Faltan channels: `security`, `audit`, `jobs`, `python` | BAJA |
| H9 | **Observabilidad** | §95 | Sin métricas de requests/latencia/jobs fallidos/usuarios activos. Laravel Telescope o similar | ALTA |
| H10 | **Backup automatizado** | §96 | Sin estrategia de backup. Artisan command o Spatie Laravel Backup + cron | ALTA |
| H11 | **Retención de audit_logs** | §62 | Sin política de retención ni archivado/particionado de `audit_logs` y `query_logs` | ALTA |
| H12 | **Concurrencia — inscripción** | §93 | `InscripcionController` usa transaction pero sin `lockForUpdate` en verificación de cupo | MEDIA |
| H13 | **Modo oscuro — aplicación** | §12 | `system_settings` tiene campos pero `app.blade.php` no aplica clase `dark` ni toggle por usuario | BAJA |
| H14 | **Redis como queue/cache driver** | §3 §111 | `.env` usa `database` driver. Redis pendiente para producción (mayor rendimiento y confiabilidad) | MEDIA |

---

## I) DASHBOARD ENRIQUECIDO POR ROL §70-§75

> `dashboard.blade.php` existe pero muestra stat cards básicas iguales para todos los roles.

| # | Dashboard | §Spec | Qué implementar | Complejidad |
|---|-----------|-------|-----------------|-------------|
| I1 | **Superadmin** | §73 | Panel organizaciones globales, escuelas, sedes, usuarios, seguridad, accesos anómalos recientes | ALTA |
| I2 | **Directivo** | §73 | Indicadores deserción/reprobación/asistencia por sede, comparativa grupos, evolución temporal | ALTA |
| I3 | **Control escolar** | §73 | Inscripciones pendientes, documentos por autorizar, bajas recientes, calendario próximo | MEDIA |
| I4 | **Docente** | §73 | Mis grupos hoy, horario del día, alumnos con asistencias pendientes, calificaciones abiertas, alertas riesgo | ALTA |
| I5 | **Cajero** | §73 | Caja activa/cerrada, pagos del día, adeudos vencidos, total del turno, acceso rápido cobrar | MEDIA |
| I6 | **Dashboard de riesgo** | §75 | Distribución 4 niveles (barras progreso), materias críticas, grupos alta reprobación, tendencia deserción | ALTA |
| I7 | **Estadísticas académicas** | §74 | Vista dedicada con filtros: sede/ciclo/nivel/grado/grupo/materia/docente/periodo | ALTA |
| I8 | **MetricCard/TrendCard/AlertCard** | §70 | Componentes UI adicionales: comparativa periodos, gráfica mini trend, alertas con severidad | MEDIA |
| I9 | **Menú dinámico por permisos** | §72 | Sidebar actual necesita verificación completa de todos los ítems con `@can` apropiado | MEDIA |

---

## PLAN DE IMPLEMENTACIÓN RECOMENDADO

### Sprint 1 — Operación básica funcional (P1 — bloqueante)

```
1. finanzas/pagos/create.blade.php       (B3)
2. finanzas/cargos/create.blade.php      (B5)
3. finanzas/caja/turno.blade.php         (B7)
4. asistencias/create.blade.php          (A1.2)
5. calificaciones/create.blade.php       (A1.3)
6. docentes/create.blade.php             (A1.19)
7. grupos/create.blade.php               (A1.12)
8. alumnos/inscripcion/create.blade.php  (A1.10)
```

### Sprint 2 — Vistas de consulta y detalle (P1)

```
9. alumnos/historial-academico.blade.php (B9)
10. alumnos/boleta.blade.php             (B11)
11. grupos/show.blade.php completo       (A2.1)
12. docentes/show.blade.php completo     (A2.3)
13. trayectorias/show.blade.php          (A2.2)
14. documentos/show.blade.php + signed URL (B2)
15. finanzas/pagos/show.blade.php        (B4)
```

### Sprint 3 — Módulos críticos de negocio (P2)

```
16. CobranzaService lógica real          (C1)
17. NotificacionService SMS+Push         (C3, C4)
18. PagoService::devolverPago()          (C11)
19. SuspiciousAccessService              (C15)
20. Python estadisticas.py con MySQL     (D1)
21. Python importaciones.py real         (D4)
22. riesgo/dashboard.blade.php           (B22)
23. Dashboard docente/cajero/directivo   (I4, I5, I2)
```

### Sprint 4 — API y tests (P2-P3)

```
24. app/Http/Controllers/Api/V1/ + Resources (E1)
25. API endpoints alumnos/calificaciones/asistencias (E2.1-E2.8)
26. Tests Unit: CalificacionService, PagoService, HorarioConflict (F1.1, F1.4, F1.5)
27. Tests Feature: Login, Inscripción, Pago (F2.1, F2.4, F2.5)
28. Tests autorización multisede (F3.1-F3.4)
```

### Sprint 5 — Configuración avanzada e integraciones (P3)

```
29. Configuración institucional jerárquica (B15, H5, H6)
30. Geocercas UI + validación asistencia laboral (B16, C10, H7)
31. Bloqueo temporal de cuentas + SuspiciousAccessService (H1, H2)
32. Backup automatizado (H10)
33. Redis como queue driver (H14)
34. OAuth/SMTP real (G7, G1)
35. Python horarios.py algoritmo real (D3)
36. Tests Python completos (F4.1-F4.5)
```

---

## CRITERIO DE ACEPTACIÓN FINAL (§100)

Cuando TODO esté implementado, cada operación crítica responderá:

| Pregunta | Campo |
|----------|-------|
| ¿Quién? | user_id, user_nombre, user_email, user_rol |
| ¿Qué? | modulo, accion, evento, descripcion |
| ¿Cuándo? | created_at |
| ¿Dónde? | sede_id, sede_nombre |
| ¿IP? | ip_address |
| ¿Dispositivo? | device_id, device_type, sistema_operativo, navegador |
| ¿Ubicación? | latitud, longitud |
| ¿Precisión? | precision_metros, fuente_ubicacion |
| ¿Registro? | model, model_id, model_descripcion |
| ¿Antes? | before_data (JSON) |
| ¿Después? | after_data (JSON) |
| ¿Motivo? | motivo |
| ¿Permiso? | permission_usado, alcance_permiso |
| ¿Request ID? | request_id |
| ¿Resultado? | resultado, motivo_fallo, http_status, duracion_ms |

✅ La trazabilidad de auditoría está completamente implementada.
🔲 Los demás criterios dependen de completar los items de este documento.

---

*Generado comparando §1-§115 de `sistema_escolar_laravel_blade_livewire_python_v3.md` contra el estado real del proyecto.*
*Siguiente acción: implementar Sprint 1 — operación básica funcional.*
