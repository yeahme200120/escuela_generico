# 📚 Sistema Escolar Multisede
## Especificación funcional, técnica y arquitectura de implementación
### Laravel 12 + Blade + Bootstrap 5 + MySQL + Python

**Versión:** 4.0  
**Fecha:** 2026-09-04  
**Estado:** 🔄 En desarrollo activo — ver `sistema_escolar_implementacion_estado.md`  
**Tipo:** Plataforma genérica de gestión escolar, académica, administrativa y financiera  
**Backend principal:** Laravel 12.x / PHP 8.2+  
**Frontend administrativo:** Blade + Bootstrap 5 *(Livewire y Alpine.js descartados)*  
**Base de datos:** MySQL 8.x — 59+ tablas, **34 migraciones ejecutadas**  
**Cache / colas:** Database queue activo · Redis pendiente producción  
**Procesamiento avanzado:** Python 3.12+ / FastAPI / Workers *(directorio `python/` pendiente)*  
**Autenticación:** Laravel Sanctum 4.3  
**Estilos:** Bootstrap 5.3 + variables CSS `--se-*` dinámicas por organización  
**Arquitectura:** Modular, multisede, multirol, auditable, geolocalizada y escalable

---

## Estado de implementación resumido (2026-09-04)

| Área | Estado | Detalle |
|------|--------|---------|
| Base de datos | ✅ Completo | 34 migraciones · 59+ tablas · seeders ejecutados |
| Modelos Eloquent | ✅ Completo | 67 modelos con traits HasUuid + Auditable |
| RBAC | ✅ Completo | 11 roles · 53 permisos · Gate dinámico por slug · 5 Policies |
| Trazabilidad geo | ✅ Completo | audit_logs · access_logs · query_logs · user_sessions con lat/lon/precisión/fuente/dispositivo |
| Services backend | ✅ Completo | 16 services: auth, auditoría, finanzas, académico, Python, webhook |
| Autenticación | ✅ Completo | Login con captura geo+dispositivo · logout con revocación de sesión |
| Panel auditoría | ✅ Completo | 4 vistas: audit_logs, access_logs, sesiones, query_logs |
| Configuración apariencia | ✅ Completo | Color pickers + preview en vivo + cache |
| Controllers operativos | ⚠️ Parcial | 17/50 controllers implementados (falta académico, catálogos, usuarios, etc.) |
| Vistas funcionales | ⚠️ Parcial | 12 completas · 18 stubs · ~70 pendientes |
| Componentes UI | ⚠️ Parcial | 6/17 componentes Blade |
| Python / FastAPI | 🔲 Pendiente | Directorio `python/` no creado. PythonJobService listo para conectar |
| API REST | 🔲 Pendiente | Solo `/api/v1/user` · resto de módulos pendientes |
| Testing | 🔲 Pendiente | phpunit.xml configurado · 0 tests escritos |
| Integraciones externas | 🔲 Pendiente | SMTP real · SMS · pasarelas · LDAP · OAuth · webhooks (WebhookService listo) |

> **Bitácora completa y lista de pendientes detallada:** `sistema_escolar_implementacion_estado.md`

---

# 1. Objetivo

Construir un sistema escolar genérico capaz de administrar una institución o una red de escuelas con múltiples planteles desde una plataforma centralizada.

El sistema deberá cubrir:

- Gestión institucional.
- Escuelas.
- Sedes.
- Usuarios.
- Roles.
- Permisos.
- Alumnos.
- Padres y tutores.
- Docentes.
- Personal administrativo.
- Control escolar.
- Ciclos escolares.
- Niveles.
- Grados.
- Grupos.
- Materias.
- Planes de estudio.
- Aulas.
- Horarios.
- Asistencias.
- Calificaciones.
- Reprobaciones.
- Regularizaciones.
- Historial académico.
- Trayectoria escolar.
- Bajas temporales.
- Bajas definitivas.
- Deserciones.
- Traslados.
- Reingresos.
- Egresos.
- Documentación escolar.
- Certificados.
- Constancias.
- Cartas.
- Justificantes.
- Cobros.
- Parcialidades.
- Pagos.
- Adeudos.
- Caja.
- Cobranza.
- Recursos humanos.
- Inventario.
- Activos fijos.
- Mantenimiento.
- Comunicación.
- Admisiones.
- Reportes.
- Estadísticas.
- Auditoría.
- Seguridad.
- Geolocalización.
- Integraciones externas.

El sistema debe poder utilizarse tanto en:

- Escuelas públicas.
- Escuelas privadas.
- Colegios.
- Institutos.
- Academias.
- Centros de capacitación.
- Universidades.
- Redes educativas multisede.

---

# 2. Decisión tecnológica

La plataforma utilizará una arquitectura **Laravel First**.

```text
                    SISTEMA ESCOLAR
                          │
             ┌────────────┴────────────┐
             │                         │
       PANEL ADMINISTRATIVO       PROCESAMIENTO
             │                         │
      Blade + Livewire             Python
       + Alpine.js                 FastAPI
             │                    Workers
             │                         │
             └────────────┬────────────┘
                          │
                        Redis
                          │
                        MySQL
```

## 2.1 Stack frontend definitivo (v4.0 — sin Livewire ni Alpine.js)

**Decisión definitiva:** Blade + Bootstrap 5 + Bootstrap JS + Vanilla JS.

| Eliminado | Razón |
|-----------|-------|
| Livewire | Sin WebSockets, sin overhead de componentes reactivos, menor complejidad |
| Alpine.js | Bootstrap 5 nativo cubre dropdowns, collapse, modals, tooltips |
| Tailwind CSS | Bootstrap 5 es el estándar, menor configuración de build |

**Interactividad cubierta por Bootstrap 5 JS:**
- Modales → `data-bs-toggle="modal"`
- Collapse/acordeón → `data-bs-toggle="collapse"` (sidebar)
- Dropdowns → `data-bs-toggle="dropdown"` (menú usuario topbar)
- Tooltips → `new bootstrap.Tooltip(el)`
- Alertas autodismiss → `bootstrap.Alert`

**JS propio del sistema** (`resources/js/app.js`):
```js
window.GeoCapture.getPosition()   // GPS del navegador → Promise
window.DeviceInfo.get()           // fingerprint del dispositivo
window.DeviceInfo.getId()         // SHA-256 del fingerprint → Promise<string>
// Sidebar toggle con persistencia localStorage
// SQL modal en QueryLog
// Confirmaciones data-confirm en forms destructivos
```

**Paginación:** `Paginator::useBootstrapFive()` en AppServiceProvider.

## 2.2 Laravel

Laravel será responsable de:

- Autenticación.
- Autorización.
- Roles.
- Permisos.
- Multisede.
- Reglas de negocio.
- Persistencia.
- Transacciones.
- API.
- Auditoría.
- Sesiones.
- Documentos.
- Notificaciones.
- Jobs.
- Colas.
- Reportes operativos.

## 2.2 Blade

Blade será la base de las vistas administrativas.

Ventajas:

- Integración nativa con Laravel.
- Seguridad.
- Simplicidad.
- SEO cuando corresponda.
- Menor complejidad que una SPA.
- Renderizado eficiente.
- Reutilización mediante componentes.
- Integración directa con Policies y permisos.

## 2.3 Livewire

Livewire será responsable de interfaces dinámicas sin convertir todo el sistema en SPA.

Se utilizará para:

- Tablas.
- Filtros.
- Búsqueda.
- Paginación.
- Formularios.
- Modales.
- Wizard de procesos.
- Actualización dinámica.
- Dashboard.
- Selección dependiente.
- Operaciones CRUD.

## 2.4 Alpine.js

Alpine.js se utilizará para interacciones ligeras del navegador:

- Modales.
- Dropdowns.
- Tabs.
- Menús.
- Tooltips.
- Confirmaciones.
- Estados visuales.
- Interacciones de componentes.

## 2.5 Tailwind CSS

Tailwind será la base visual.

Debe existir un sistema de diseño global para que toda la aplicación comparta:

- Colores.
- Tipografía.
- Espaciados.
- Bordes.
- Sombras.
- Botones.
- Inputs.
- Tablas.
- Badges.
- Alertas.
- Modales.

---

# 3. Python

Python será un subsistema especializado.

No deberá reemplazar las reglas transaccionales de Laravel.

Utilizar Python para:

- Estadísticas masivas.
- Analítica académica.
- Análisis de reprobación.
- Análisis de deserción.
- Detección de alumnos en riesgo.
- Generación masiva de documentos.
- Procesamiento Excel.
- Importaciones masivas.
- Exportaciones pesadas.
- Optimización de horarios.
- Procesamiento geográfico.
- Análisis histórico.
- Procesamiento científico/estadístico.
- Modelos predictivos futuros.

Arquitectura:

```text
Laravel
   │
   ├── Job
   │
   ▼
Redis Queue
   │
   ▼
Python Worker
   │
   ▼
Resultado
   │
   ▼
Laravel
```

El navegador nunca deberá comunicarse directamente con Python.

---

# 4. Arquitectura de proyecto

```text
app/
├── Actions/
├── Console/
├── Events/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   │   └── Api/V1/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Jobs/
├── Listeners/
├── Livewire/
│   ├── Dashboard/
│   ├── Alumnos/
│   ├── Docentes/
│   ├── Grupos/
│   ├── Materias/
│   ├── Horarios/
│   ├── Calificaciones/
│   ├── Asistencias/
│   ├── ControlEscolar/
│   ├── Finanzas/
│   ├── Caja/
│   ├── RH/
│   ├── Inventario/
│   ├── Auditoria/
│   └── Configuracion/
├── Models/
├── Notifications/
├── Policies/
├── Services/
│   ├── Auth/
│   ├── Academico/
│   ├── Alumnos/
│   ├── Docentes/
│   ├── Horarios/
│   ├── ControlEscolar/
│   ├── Finanzas/
│   ├── Caja/
│   ├── RH/
│   ├── Auditoria/
│   ├── Seguridad/
│   └── Python/
└── Support/
```

---

# 5. Arquitectura de recursos visuales

```text
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── guest.blade.php
│   │   └── partials/
│   │
│   ├── components/
│   │   ├── ui/
│   │   ├── forms/
│   │   ├── tables/
│   │   ├── modals/
│   │   ├── cards/
│   │   ├── badges/
│   │   ├── alerts/
│   │   ├── navigation/
│   │   └── audit/
│   │
│   └── livewire/
│
├── css/
│   ├── app.css
│   ├── theme.css
│   └── components.css
│
└── js/
    ├── app.js
    └── alpine/
```

---

# 6. Sistema de componentes reutilizables

Este requisito será obligatorio.

No desarrollar cada pantalla desde cero.

## 6.1 Componentes Blade

Crear componentes:

```text
x-layout
x-page-header
x-card
x-button
x-input
x-select
x-textarea
x-checkbox
x-radio
x-date-picker
x-search
x-filter
x-modal
x-confirm
x-alert
x-badge
x-table
x-pagination
x-empty-state
x-loading
x-tabs
x-dropdown
x-stat-card
x-chart
x-file-upload
x-avatar
x-breadcrumb
```

Ejemplo:

```blade
<x-ui.button variant="primary">
    Guardar
</x-ui.button>
```

---

# 7. Sistema global de colores

Toda la aplicación deberá utilizar tokens de diseño.

Nunca colocar colores directamente en cada componente.

Incorrecto:

```html
class="bg-blue-600"
```

Correcto conceptualmente:

```html
class="bg-primary"
```

o mediante variables CSS:

```css
var(--color-primary)
```

---

# 8. Configuración global del tema

Crear:

```text
config/theme.php
```

Ejemplo conceptual:

```php
return [
    'primary' => '#2563EB',
    'secondary' => '#64748B',
    'success' => '#16A34A',
    'warning' => '#D97706',
    'danger' => '#DC2626',
    'info' => '#0891B2',
    'background' => '#F8FAFC',
    'surface' => '#FFFFFF',
    'text' => '#0F172A',
];
```

Los valores reales deberán poder administrarse desde configuración.

---

# 9. Personalización de colores por Superadmin

Solamente el `superadmin` podrá cambiar la identidad visual global.

Ruta:

```text
/configuracion/apariencia
```

Opciones:

- Color primario.
- Color secundario.
- Color de éxito.
- Color de advertencia.
- Color de peligro.
- Color informativo.
- Fondo.
- Superficies.
- Texto.
- Logo.
- Favicon.
- Nombre institucional.

## 9.1 Seguridad

No permitir:

```text
director
administrador
control_escolar
docente
cajero
administrativo
```

modificar la identidad visual global salvo que explícitamente se cree posteriormente un permiso especial.

El permiso base será:

```text
configuracion.apariencia.editar
```

asignado únicamente a `superadmin`.

---

# 10. Tema dinámico

Guardar:

```text
system_settings
- id
- organizacion_id
- key
- value
- type
- updated_by
- updated_at
```

Ejemplo:

```text
theme.primary
theme.secondary
theme.success
theme.warning
theme.danger
theme.logo
theme.favicon
```

En Blade:

```text
Base Layout
     ↓
Carga configuración
     ↓
Variables CSS
     ↓
Todos los componentes
```

---

# 11. Variables CSS

Ejemplo conceptual:

```css
:root {
    --color-primary: #2563EB;
    --color-secondary: #64748B;
    --color-success: #16A34A;
    --color-warning: #D97706;
    --color-danger: #DC2626;
    --color-info: #0891B2;
}
```

Los componentes deberán consumir estas variables.

Esto permite cambiar la identidad visual sin modificar cada vista.

---

# 12. Temas y modo oscuro

Preparar soporte para:

```text
light
dark
system
```

El superadmin podrá habilitar/deshabilitar el modo oscuro para la organización.

El usuario podrá seleccionar preferencia personal si la política institucional lo permite.

---

# 13. Multisede

Jerarquía:

```text
Organización
│
├── Escuela
│   ├── Sede
│   │   ├── Edificios
│   │   ├── Aulas
│   │   └── Personal
│   │
│   └── Sede
│
├── Ciclos
└── Planes de estudio
```

Tablas:

```text
organizaciones
escuelas
sedes
edificios
aulas
```

---

# 14. Usuarios

```text
users
- id
- organizacion_id
- nombres
- apellido_paterno
- apellido_materno
- email
- username
- telefono
- password
- activo
- ultimo_acceso_at
- email_verified_at
- created_at
- updated_at
```

---

# 15. Usuarios multisede

```text
user_sede
- id
- user_id
- sede_id
- principal
- activo
```

Permite:

- Directivos regionales.
- Docentes itinerantes.
- Administradores multisede.
- Personal compartido.

---

# 16. Roles

Roles iniciales:

```text
superadmin
directivo
administrador
control_escolar
docente
cajero
administrativo
rh
alumno
tutor
soporte
```

---

# 17. RBAC

Tablas:

```text
roles
permissions
role_permission
user_role
```

Permisos:

```text
alumnos.ver
alumnos.crear
alumnos.editar
alumnos.eliminar

calificaciones.ver
calificaciones.registrar
calificaciones.editar
calificaciones.cerrar
calificaciones.autorizar

horarios.ver
horarios.crear
horarios.editar
horarios.publicar

pagos.ver
pagos.registrar
pagos.cancelar
pagos.devolver

auditoria.ver
```

---

# 18. Alcance de permisos

Un permiso puede aplicarse a:

```text
global
organizacion
escuela
sede
ciclo
grupo
propio
```

Ejemplo:

```text
docente
    calificaciones.registrar
    alcance = grupos_asignados
```

---

# 19. Políticas Laravel

Todos los recursos críticos deberán tener Policies.

Ejemplos:

```text
AlumnoPolicy
DocentePolicy
GrupoPolicy
MateriaPolicy
CalificacionPolicy
AsistenciaPolicy
PagoPolicy
CajaPolicy
DocumentoPolicy
AuditoriaPolicy
```

El frontend jamás será la autoridad final.

---

# 20. Alumnos

```text
alumnos
- id
- organizacion_id
- matricula
- curp
- nombres
- apellido_paterno
- apellido_materno
- fecha_nacimiento
- sexo
- email
- telefono
- direccion
- fecha_ingreso
- estatus
- sede_actual_id
```

---

# 21. Estados del alumno

No utilizar un único campo para representar toda la trayectoria.

Separar:

## Estatus administrativo

```text
activo
baja_temporal
baja_definitiva
egresado
```

## Situación académica

```text
regular
irregular
reprobado
en_regularizacion
condicionado
```

## Situación de inscripción

```text
inscrito
reinscrito
pendiente
no_reinscrito
cancelada
```

---

# 22. Trayectoria escolar

Crear:

```text
trayectorias_alumno
```

Campos:

```text
id
alumno_id
ciclo_escolar_id
sede_id
grado_id
grupo_id
estatus
situacion_academica
fecha_inicio
fecha_fin
motivo
observaciones
usuario_id
created_at
updated_at
```

La trayectoria deberá conservar el historial completo.

Nunca borrar la historia académica por una baja.

---

# 23. Historial de grupos

```text
alumno_grupo_historial
- id
- alumno_id
- grupo_id
- ciclo_escolar_id
- fecha_inicio
- fecha_fin
- motivo
- usuario_id
```

Permite reconstruir:

```text
Alumno
 ↓
Sede
 ↓
Grado
 ↓
Grupo
 ↓
Ciclo
```

---

# 24. Bajas temporales

Una baja temporal deberá conservar:

```text
fecha_inicio
fecha_fin_estimada
fecha_reingreso
motivo
observaciones
solicitado_por
autorizado_por
```

Estados:

```text
solicitada
aprobada
activa
reingresado
cancelada
```

El alumno podrá regresar sin perder su historial.

---

# 25. Bajas definitivas

Registrar:

```text
tipo
fecha
motivo
observaciones
documento
usuario_solicita
usuario_autoriza
```

Nunca eliminar al alumno físicamente.

---

# 26. Deserción

La deserción será un concepto independiente.

Motivos:

```text
abandono
inasistencia_prolongada
problemas_economicos
problemas_familiares
cambio_de_ciudad
cambio_de_escuela
bajo_aprovechamiento
motivo_personal
otro
```

Debe ser posible distinguir:

```text
baja definitiva
deserción
traslado
baja temporal
egreso
```

---

# 27. Reingreso

Un alumno con baja temporal o determinadas bajas podrá iniciar un proceso de reingreso.

Registrar:

```text
alumno_id
fecha_solicitud
fecha_reingreso
sede
grado
grupo
ciclo
usuario
motivo
```

La trayectoria anterior permanecerá intacta.

---

# 28. Reprobaciones

Las calificaciones deben generar un resultado académico.

```text
APROBADO
REPROBADO
NP
NA
EXTRAORDINARIO
REGULARIZADO
```

Crear historial de regularización.

```text
regularizaciones
- id
- alumno_id
- materia_id
- ciclo_escolar_id
- calificacion_original
- calificacion_regularizacion
- fecha
- resultado
- usuario_id
```

---

# 29. Riesgo académico

El sistema deberá identificar alumnos con:

- Materias reprobadas.
- Bajo promedio.
- Baja asistencia.
- Reprobaciones consecutivas.
- Regularizaciones pendientes.
- Caída significativa del rendimiento.

Estados:

```text
normal
observacion
riesgo_medio
riesgo_alto
```

El motor inicial será basado en reglas.

Python podrá incorporar modelos predictivos posteriormente.

---

# 30. Indicadores de permanencia

Calcular:

```text
alumnos inscritos
alumnos activos
alumnos reprobados
materias reprobadas
bajas temporales
bajas definitivas
deserciones
traslados
reingresos
egresados
```

Indicadores:

```text
% aprobación
% reprobación
% deserción
% retención
% permanencia
% abandono
% egreso
```

---

# 31. Niveles, grados y grupos

```text
niveles_educativos
grados
grupos
```

Grupos:

```text
id
sede_id
ciclo_escolar_id
grado_id
nombre
turno
capacidad
aula_principal_id
docente_tutor_id
activo
```

---

# 32. Materias

```text
materias
- id
- escuela_id
- clave
- nombre
- descripcion
- horas_semana
- creditos
- tipo
- activa
```

Tipos:

```text
obligatoria
optativa
taller
extracurricular
```

---

# 33. Planes de estudio

```text
planes_estudio
plan_materias
```

Permitir homologación entre sedes.

---

# 34. Docentes

```text
docentes
- id
- user_id
- numero_empleado
- especialidad
- cedula
- fecha_ingreso
- tipo_contrato
- estatus
```

---

# 35. Asignación docente

```text
docente_grupo_materia
- id
- docente_id
- grupo_id
- materia_id
- ciclo_escolar_id
- sede_id
- horas_semana
- activo
```

Esta relación controla:

- Materias.
- Grupos.
- Calificaciones.
- Horarios.
- Estadísticas docentes.

---

# 36. Horarios

Tablas:

```text
horario_bloques
horarios
```

Campos principales:

```text
grupo
materia
docente
aula
día
hora_inicio
hora_fin
ciclo
```

---

# 37. Validación de colisiones

Crear:

```text
HorarioConflictService
```

Debe impedir:

```text
Docente ocupado
Aula ocupada
Grupo ocupado
Materia duplicada
Capacidad insuficiente
Horario fuera de disponibilidad
```

---

# 38. Generación automática de horarios

Flujo:

```text
Configuración
 ↓
Restricciones
 ↓
Laravel Job
 ↓
Python
 ↓
Algoritmo de optimización
 ↓
Propuesta
 ↓
Validación Laravel
 ↓
Autorización
 ↓
Publicación
```

Nunca publicar directamente el resultado de Python sin validación final de Laravel.

---

# 39. Asistencias

```text
asistencias
- id
- alumno_id
- grupo_id
- materia_id
- docente_id
- fecha
- estado
- hora_registro
- observacion
- registrado_por
```

Estados:

```text
presente
falta
retardo
justificada
```

---

# 40. Justificantes

```text
justificantes
- id
- alumno_id
- fecha_inicio
- fecha_fin
- motivo
- documento
- estado
- solicitado_por
- autorizado_por
```

Una justificación aprobada no deberá borrar la asistencia original.

---

# 41. Calificaciones

```text
periodos_evaluacion
calificaciones
```

Cada calificación deberá conservar:

```text
alumno
grupo
materia
docente
periodo
calificacion
resultado
usuario_registra
usuario_actualiza
timestamps
```

Los periodos cerrados requerirán permiso especial para modificaciones.

---

# 42. Historial académico

Debe poder consultarse:

```text
por alumno
por grupo
por grado
por ciclo
por materia
por docente
por sede
```

---

# 43. Estadísticas docentes

Indicadores:

```text
alumnos atendidos
promedio
aprobación
reprobación
asistencia
materias impartidas
grupos
evolución
```

Permitir comparar:

```text
Docente
 ↓
Grupo
 ↓
Materia
 ↓
Ciclo
```

---

# 44. Control escolar

Funciones:

- Inscripciones.
- Reinscripciones.
- Cambios de grupo.
- Cambios de sede.
- Bajas.
- Reingresos.
- Actas.
- Calificaciones.
- Asistencias.
- Expedientes.
- Documentos.
- Certificados.
- Constancias.
- Justificantes.
- Folios.
- Autorizaciones.

---

# 45. Documentos escolares

Catálogo:

```text
Carta de buena conducta
Constancia de estudios
Constancia de inscripción
Certificado
Kardex
Historial académico
Justificante
Carta de baja
Carta de no adeudo
Boleta
Acta
Reconocimiento
Documento personalizado
```

---

# 46. Expediente documental

```text
documentos
- id
- alumno_id
- tipo_documento_id
- folio
- version
- archivo
- hash_archivo
- estado
- generado_por
- autorizado_por
- generado_at
- autorizado_at
```

---

# 47. Finanzas opcionales

El módulo financiero debe poder activarse o desactivarse por organización.

Conceptos:

```text
Inscripción
Reinscripción
Colegiatura
Transporte
Seguro
Uniformes
Libros
Talleres
Exámenes
Certificados
Constancias
Otros
```

---

# 48. Cargos

```text
cargos
- id
- alumno_id
- ciclo_escolar_id
- concepto_id
- referencia
- importe
- descuento
- recargo
- total
- fecha_vencimiento
- estado
```

---

# 49. Parcialidades

```text
parcialidades
- id
- cargo_id
- numero
- fecha_vencimiento
- importe
- estado
```

---

# 50. Pagos

```text
pagos
- id
- alumno_id
- caja_id
- referencia
- importe
- fecha_pago
- metodo_pago_id
- usuario_id
- estado
```

Aplicación:

```text
pago
 ↓
pago_detalle
 ↓
cargo
```

Un pago puede cubrir múltiples conceptos.

---

# 51. Caja

```text
cajas
turnos_caja
movimientos_caja
```

Funciones:

- Apertura.
- Ingresos.
- Egresos.
- Retiros.
- Devoluciones.
- Arqueo.
- Cierre.

---

# 52. Operaciones financieras críticas

Requieren:

- Permiso.
- Usuario.
- Motivo.
- Auditoría.
- Request ID.
- Transaction.
- Idempotencia.

Ejemplos:

```text
cancelar pago
devolver pago
modificar cargo
aplicar descuento
cerrar caja
ajustar caja
```

---

# 53. Recursos humanos

```text
empleados
contratos
asistencia_personal
```

Docentes itinerantes podrán trabajar en varias sedes.

---

# 54. Geolocalización

Registrar cuando funcionalmente corresponda:

```text
latitud
longitud
precision_metros
altitud
velocidad
heading
ip
dispositivo
```

No utilizar geolocalización como única prueba de identidad.

---

# 55. Accesos

Tabla:

```text
access_logs
```

Campos:

```text
id
user_id
session_id
sede_id
ip_address
user_agent
dispositivo_id
sistema_operativo
navegador
latitud
longitud
precision_metros
fuente_ubicacion
resultado
motivo_rechazo
created_at
```

---

# 56. Geocercas

Una sede podrá definir:

```text
latitud
longitud
radio_permitido_metros
```

Se podrá utilizar para:

- Registro de asistencia laboral.
- Control de acceso.
- Procesos específicos definidos por la organización.

La geolocalización deberá tener finalidad y configuración de retención.

---

# 57. Sesiones

```text
user_sessions
- id
- user_id
- token_id
- device_id
- ip_address
- user_agent
- first_seen_at
- last_seen_at
- last_latitude
- last_longitude
- last_accuracy
- revoked_at
- active
```

Funciones:

- Ver sesiones.
- Revocar.
- Cerrar dispositivos.
- Detectar accesos anómalos.

---

# 58. Auditoría

Crear:

```text
audit_logs
```

Campos:

```text
id
uuid
user_id
session_id
organization_id
school_id
sede_id
module
action
event
model
model_id
description
before_data
after_data
changes
ip_address
user_agent
device_id
latitude
longitude
accuracy
result
reason
request_id
created_at
```

---

# 59. Eventos auditables

Como mínimo:

```text
login
logout
login_failed

create
update
delete
restore

approve
reject
authorize
cancel
refund
open
close

export
download
print
generate
publish
unpublish
```

---

# 60. Auditoría before/after

Ejemplo:

```json
{
    "before": {
        "calificacion": 6.5
    },
    "after": {
        "calificacion": 8.5
    }
}
```

Debe quedar:

```text
Quién
Cuándo
Dónde
IP
Dispositivo
Ubicación
Precisión
Registro
Antes
Después
Motivo
Permiso
Request ID
```

---

# 61. Request ID

Toda petición deberá generar:

```text
X-Request-ID
```

Ejemplo:

```text
REQ-01JXXXXXXX
```

El identificador debe propagarse a:

- Logs.
- Auditoría.
- Jobs.
- Eventos.
- Python.

---

# 62. Auditoría inmutable

No implementar eliminación normal de auditorías.

No existir:

```text
DELETE /audit-logs/{id}
```

La conservación deberá utilizar:

- Retención.
- Archivado.
- Particionado.
- Backups.

---

# 63. Seguridad

Implementar:

- Sanctum.
- Password hashing.
- Rate limiting.
- Bloqueo temporal.
- Recuperación de contraseña.
- 2FA opcional.
- Revocación de sesiones.
- Control de dispositivos.
- Políticas de contraseña.
- Auditoría.
- Alertas.

Nunca registrar:

- Contraseñas.
- Tokens.
- Secretos.
- Credenciales.

---

# 64. Detección de accesos sospechosos

Detectar:

```text
Muchos intentos fallidos
Nuevo dispositivo
Cambio geográfico abrupto
Actividad fuera de horario
Múltiples sesiones
Distancia físicamente imposible
```

Python podrá analizar secuencias históricas.

---

# 65. Componentes de tablas

Crear un componente reutilizable:

```text
x-table
```

Debe soportar:

- Columnas.
- Acciones.
- Ordenamiento.
- Búsqueda.
- Filtros.
- Estados.
- Slots.
- Responsive.
- Selección.
- Acciones masivas.

Livewire manejará:

```text
search
sort
filters
paginate
```

---

# 66. Paginación

Todas las listas grandes deberán utilizar:

```php
->paginate(25)
```

El máximo configurable deberá ser limitado.

Ejemplo:

```text
25
50
100
```

Exportaciones grandes serán asíncronas.

---

# 67. DataTables

DataTables no será obligatorio.

Se utilizará únicamente cuando aporte valor:

- Server-side processing.
- Grandes volúmenes.
- Ordenamiento avanzado.
- Exportaciones.
- Columnas dinámicas.

La mayoría de CRUD administrativos utilizarán:

```text
Blade
+
Livewire
+
Laravel Pagination
```

---

# 68. Formularios reutilizables

Crear componentes para:

```text
AlumnoForm
DocenteForm
GrupoForm
MateriaForm
SedeForm
UsuarioForm
PagoForm
DocumentoForm
```

Cuando sea posible, separar:

```text
Formulario
Validación
Persistencia
Auditoría
```

---

# 69. Modales reutilizables

Ejemplos:

```text
ConfirmDeleteModal
ConfirmActionModal
FormModal
DetailModal
AuditModal
DocumentPreviewModal
```

Toda acción destructiva deberá solicitar confirmación.

Las acciones críticas deberán solicitar motivo.

---

# 70. Cards y dashboard

Crear:

```text
StatCard
MetricCard
TrendCard
AlertCard
ProgressCard
```

Ejemplo:

```text
┌────────────────────┐
│ Alumnos activos    │
│ 1,250              │
│ ↑ 4.2%             │
└────────────────────┘
```

---

# 71. Sistema de notificaciones visuales

Componente global:

```text
Toast
Alert
NotificationCenter
```

Tipos:

```text
success
info
warning
danger
```

---

# 72. Menú dinámico por permisos

El menú deberá construirse según permisos.

Ejemplo:

```text
Académico
  ├── Grupos
  ├── Materias
  ├── Horarios
  ├── Asistencias
  └── Calificaciones
```

Un usuario sin permiso no verá el módulo.

Pero Laravel seguirá validando el acceso.

---

# 73. Dashboard por rol

## Superadmin

- Organizaciones.
- Escuelas.
- Sedes.
- Usuarios.
- Seguridad.
- Auditoría.
- Configuración global.

## Directivo

- Alumnos.
- Docentes.
- Rendimiento.
- Asistencia.
- Deserción.
- Reprobación.
- Finanzas según permiso.

## Control escolar

- Inscripciones.
- Documentos.
- Calificaciones.
- Actas.
- Historial.

## Docente

- Mis grupos.
- Horario.
- Asistencia.
- Calificaciones.
- Estadísticas.

## Cajero

- Caja.
- Pagos.
- Adeudos.
- Cobranza.

---

# 74. Estadísticas académicas

Indicadores:

```text
Promedio
Aprobación
Reprobación
Asistencia
Deserción
Retención
Permanencia
```

Filtros:

```text
Sede
Ciclo
Nivel
Grado
Grupo
Materia
Docente
Periodo
```

---

# 75. Dashboard de riesgo

Mostrar:

```text
Alumnos en riesgo
Materias críticas
Grupos con alta reprobación
Grupos con baja asistencia
Aumento de deserción
```

Ejemplo:

```text
RIESGO ACADÉMICO

Alto       24
Medio      51
Observación 83
Normal     932
```

---

# 76. Procesamiento Python académico

Python podrá ejecutar:

```text
calcular_indicadores
calcular_riesgo
analizar_reprobacion
analizar_desercion
generar_reportes
```

Resultado:

```json
{
    "job_id": "JOB-123",
    "status": "completed",
    "results": {
        "approval_rate": 87.4,
        "failure_rate": 12.6,
        "dropout_rate": 4.2
    }
}
```

---

# 77. Importaciones masivas

Formato:

```text
Excel
CSV
```

Flujo:

```text
Subir
 ↓
Validar
 ↓
Previsualizar
 ↓
Confirmar
 ↓
Queue
 ↓
Python
 ↓
Procesar
 ↓
Reporte
```

---

# 78. Reportes masivos

No generar reportes grandes en HTTP.

Usar:

```text
Laravel
 ↓
Job
 ↓
Python
 ↓
Archivo privado
 ↓
Notificación
 ↓
Descarga autorizada
```

---

# 79. Exportaciones

Registrar:

```text
Quién exportó
Qué exportó
Cuándo
Sede
Filtros
Número de registros
Archivo
Request ID
```

Una exportación deberá ser una acción auditable.

---

# 80. Archivos privados

Documentos escolares y financieros deberán utilizar almacenamiento privado.

Descarga:

```text
Usuario
 ↓
Permiso
 ↓
Policy
 ↓
Documento
 ↓
Signed URL
 ↓
Download
 ↓
Audit
```

---

# 81. Inventario

Controlar:

- Libros.
- Uniformes.
- Papelería.
- Consumibles.
- Equipos.

Por sede.

Movimientos:

```text
entrada
salida
transferencia
ajuste
```

---

# 82. Activos fijos

Registrar:

```text
codigo
nombre
categoria
numero_serie
sede
edificio
aula
responsable
valor
estado
```

Transferencias entre sedes deberán ser auditadas.

---

# 83. Mantenimiento

Registrar:

```text
sede
edificio
aula
activo
reporte
prioridad
estado
responsable
fechas
```

---

# 84. Admisiones

CRM:

```text
prospectos
seguimientos
citas
evaluaciones
admisiones
```

Registrar sede de interés.

---

# 85. Comunicación

Canales:

```text
Email
SMS
Push
Notificaciones internas
```

Segmentación:

```text
Organización
Escuela
Sede
Nivel
Grado
Grupo
Docentes
Alumnos
Tutores
```

---

# 86. Calendario escolar

Permitir calendarios independientes por sede.

Eventos:

```text
Inicio de clases
Fin
Vacaciones
Suspensión
Exámenes
Consejo técnico
Eventos
Festivos locales
```

---

# 87. Configuración institucional

Configuraciones:

```text
Escala de calificación
Mínimo aprobatorio
Periodos
Tolerancia de retardo
Calendario
Moneda
Documentos
Geocerca
Políticas
Tema visual
```

---

# 88. Herencia de configuración

Orden:

```text
Organización
 ↓
Escuela
 ↓
Sede
 ↓
Ciclo
```

La configuración más específica tiene prioridad cuando la regla lo permita.

---

# 89. Integraciones

Preparar:

```text
Moodle
Canvas
Bancos
Pasarelas
SMTP
SMS
Push
Facturación
LDAP
OAuth
```

---

# 90. Webhooks

Crear:

```text
webhook_events
```

Características:

- Validación de firma.
- Idempotencia.
- Request ID.
- Auditoría.
- Reintentos.

---

# 91. Idempotencia

Especialmente para:

```text
Pagos
Webhooks
Inscripciones
Generación de documentos
Sincronización
Integraciones
```

Usar:

```text
Idempotency-Key
```

---

# 92. Transacciones

Operaciones críticas:

```php
DB::transaction(function () {
    // operación
});
```

Ejemplo de pago:

```text
Crear pago
 ↓
Aplicar pago
 ↓
Actualizar cargo
 ↓
Movimiento caja
 ↓
Auditoría
 ↓
Commit
```

---

# 93. Concurrencia

Utilizar cuando corresponda:

- Unique constraints.
- Transactions.
- Locks.
- Idempotency.
- Optimistic concurrency.

Especialmente para:

- Caja.
- Pagos.
- Calificaciones.
- Inscripciones.
- Horarios.

---

# 94. Logs

Separar:

```text
application logs
security logs
audit logs
job logs
python logs
```

Los logs técnicos no sustituyen la auditoría funcional.

---

# 95. Observabilidad

Medir:

```text
requests
errores
latencia
jobs
jobs fallidos
usuarios activos
procesamiento Python
```

---

# 96. Backups

Implementar:

- Backup diario.
- Retención.
- Copias externas.
- Pruebas de restauración.

Un backup no deberá considerarse válido sin verificar restauración periódicamente.

---

# 97. Testing Laravel

## Unit

- Servicios.
- Cálculos.
- Reglas.

## Feature

- API.
- Login.
- Alumnos.
- Pagos.
- Calificaciones.

## Authorization

Probar:

```text
Docente no accede a grupo ajeno.
Cajero no modifica calificaciones.
Alumno no consulta otro alumno.
Sede A no consulta información restringida de Sede B.
```

---

# 98. Testing Livewire

Probar:

- Filtros.
- Paginación.
- Validaciones.
- Modales.
- Permisos.
- Formularios.
- Acciones masivas.

---

# 99. Testing Python

Probar:

- Estadísticas.
- Importaciones.
- Horarios.
- Reportes.
- Geografía.
- Riesgo académico.

---

# 100. Auditoría como criterio de aceptación

Toda operación crítica deberá responder:

```text
¿Quién?
¿Qué?
¿Cuándo?
¿Dónde?
¿Desde qué IP?
¿Desde qué dispositivo?
¿Con qué ubicación?
¿Con qué precisión?
¿Sobre qué registro?
¿Qué había antes?
¿Qué quedó después?
¿Por qué?
¿Con qué permiso?
¿Qué Request ID?
¿Terminó correctamente?
```

---

# 101. Criterios de seguridad del frontend

El frontend:

```text
NO es autoridad.
```

Puede:

```text
ocultar botones
ocultar menús
deshabilitar acciones
```

Pero Laravel siempre deberá validar:

```text
Authentication
Authorization
Policy
Scope
Sede
Recurso
Estado
```

---

# 102. Estado de implementación por fase (actualizado 2026-09-04)

## Fase 1 — Núcleo ✅ COMPLETADO
```
✅ Laravel 12 · MySQL · Sanctum · Bootstrap 5 · Vite
✅ 34 migraciones ejecutadas · 59+ tablas
✅ 67 modelos Eloquent con HasUuid + Auditable
✅ RBAC: 11 roles + 53 permisos + Gate dinámico + 5 Policies
✅ Trazabilidad completa: geo + dispositivo + queries en cada request
✅ AuthService: login con captura geo/device · logout con revocación
✅ Middleware pipeline: SetRequestId → GeoTrace → CheckUserActive
✅ Panel de auditoría: audit_logs · access_logs · sesiones · query_logs
✅ Configuración apariencia: color pickers + preview vivo + cache
✅ Seeders: roles/permisos + org demo + 4 usuarios de prueba
```

## Fase 2 — Catálogos ✅ COMPLETADO (BD y modelos)
```
✅ Tablas: materias · planes_estudio · plan_materias
✅ Modelos: Materia · PlanEstudio · PlanMateria
⚠️ Controllers y vistas: pendientes
```

## Fase 3 — Trayectoria académica ✅ COMPLETADO (BD y modelos)
```
✅ Tablas: alumnos · tutores · docentes · docente_grupo_materia
✅       trayectorias_alumno · alumno_grupo_historial · bajas · reingresos
✅ Modelos: Alumno · Tutor · Docente · DocenteGrupoMateria
✅         TrayectoriaAlumno · AlumnoGrupoHistorial · Baja · Reingreso
✅ Services: BajaService (preserva historial) · InscripcionController
✅ AlumnoController CRUD completo · InscripcionController con DB::transaction
⚠️ Vistas: stubs · faltan formularios completos y ficha alumno
```

## Fase 4 — Operación académica ✅ COMPLETADO (BD, modelos, services)
```
✅ Tablas: horario_bloques · horarios · asistencias · justificantes
✅       periodos_evaluacion · calificaciones · regularizaciones
✅ Modelos: Horario · HorarioBloque · Asistencia · Justificante
✅          PeriodoEvaluacion · Calificacion · Regularizacion
✅ HorarioConflictService: 5 tipos de colisión
✅ AsistenciaService: registrarLista · aplicarJustificante sin borrar original
✅ CalificacionService: registrar · cerrarPeriodo con lock
✅ RiesgoAcademicoService: motor de reglas 4 niveles + calcularMasivo
✅ IndicadoresService: % aprobación/deserción/permanencia/retención
⚠️ Controllers y vistas: pendientes (Horario, Asistencia, Calificacion)
```

## Fase 5 — Control escolar ✅ COMPLETADO (BD, modelos, services)
```
✅ Tablas: tipos_documento · documentos · folios
✅ Modelos: TipoDocumento · Documento · Folio
✅ DocumentoService: generarFolio (lockForUpdate) · crear · autorizar
✅ DocumentoController: index · store · autorizar
⚠️ Vista documentos: stub · falta signed URL + preview
```

## Fase 6 — Finanzas ✅ COMPLETADO (BD, modelos, services)
```
✅ Tablas: conceptos_pago · cargos · parcialidades · metodos_pago
✅        pagos · pago_detalle · cajas · turnos_caja · movimientos_caja
✅ PagoService: idempotencia SHA-256 · reversión de cargos
✅ CajaService: abrir · cerrar con monto_esperado · registrarMovimiento
✅ CargoController · PagoController · CajaController
⚠️ Vistas: stubs · faltan formularios completos
```

## Fase 7 — Python ⚠️ ESTRUCTURA LISTA · WORKERS PENDIENTES
```
✅ Tabla python_jobs (estado · payload · resultado · progreso)
✅ Modelo PythonJob
✅ PythonJobService: despachar · ejecutar HTTP FastAPI · obtenerEstado
✅ Job DispatchPythonJob: ShouldQueue · tries=3 · timeout=600
✅ config/python.php: url · secret · timeout · queue
🔲 Directorio python/ no existe
🔲 FastAPI app/main.py
🔲 Workers: indicadores · riesgo · importaciones · horarios · reportes
🔲 python/requirements.txt · python/tests/
```

## Fase 8 — Administración avanzada ✅ COMPLETADO (BD y modelos)
```
✅ RH: empleados · contratos · asistencia_personal · EmpleadoController
✅ Inventario: categorias_inventario · inventario · movimientos · activos_fijos
✅ Comunicación: notificaciones · notificacion_usuario · calendario_escolar
✅ Admisiones: prospectos · seguimientos · admisiones · ProspectoController
✅ Mantenimiento: tabla + modelo
✅ Integraciones: webhook_events + WebhookService (HMAC · idempotencia · reintentos)
⚠️ Vistas: stubs · faltan formularios completos
🔲 NotificacionService: envío multicanal con segmentación
🔲 Integraciones externas: SMTP real · SMS · pasarelas · LDAP · OAuth
```

---

# 102b. Estrategia de desarrollo

No comenzar por dashboards.

Orden recomendado:

```text
1. Laravel
2. MySQL
3. Migraciones
4. Modelos
5. Autenticación
6. Usuarios
7. Roles
8. Permisos
9. Multisede
10. Policies
11. Auditoría
12. Sesiones
13. Sistema de componentes
14. Tema global
15. Catálogos
16. Alumnos
17. Docentes
18. Grupos
19. Materias
20. Inscripciones
21. Trayectoria
22. Horarios
23. Asistencias
24. Calificaciones
25. Reprobaciones
26. Regularizaciones
27. Control escolar
28. Documentos
29. Finanzas
30. Caja
31. Reportes
32. Python
33. RH
34. Inventario
35. Comunicación
36. Admisiones
37. Integraciones
```

---

# 103. Fase 1 — Núcleo

Implementar primero:

```text
Laravel 12
MySQL
Redis
Sanctum
Blade
Livewire
Alpine.js
Tailwind
```

Después:

```text
Usuarios
Roles
Permisos
Multisede
Policies
Auditoría
Sesiones
Request ID
Tema global
Componentes reutilizables
```

---

# 104. Fase 2 — Catálogos

```text
Organización
Escuela
Sede
Edificio
Aula
Ciclo
Nivel
Grado
Grupo
Materia
Plan de estudio
```

---

# 105. Fase 3 — Trayectoria académica

```text
Alumnos
Tutores
Docentes
Inscripciones
Grupos
Asignaciones
Historial
Reprobaciones
Regularizaciones
Bajas
Deserciones
Reingresos
```

---

# 106. Fase 4 — Operación académica

```text
Horarios
Asistencias
Calificaciones
Actas
Boletas
Historial
Estadísticas
```

---

# 107. Fase 5 — Control escolar

```text
Expedientes
Justificantes
Constancias
Cartas
Certificados
Folios
Autorizaciones
```

---

# 108. Fase 6 — Finanzas

```text
Conceptos
Cargos
Parcialidades
Pagos
Adeudos
Caja
Cobranza
```

---

# 109. Fase 7 — Python

```text
Importaciones
Estadísticas
Reportes
Horarios
Reprobación
Deserción
Riesgo académico
Geoprocesamiento
```

---

# 110. Fase 8 — Administración avanzada

```text
RH
Inventario
Activos
Mantenimiento
Comunicación
Admisiones
Integraciones
```

---

# 111. Regla arquitectónica principal

El sistema debe separar claramente:

```text
PRESENTACIÓN
Blade + Livewire + Alpine.js

NEGOCIO
Laravel Services + Actions + Policies

DATOS
Eloquent + MySQL

ASINCRONÍA
Redis + Laravel Queue

PROCESAMIENTO PESADO
Python

AUDITORÍA
AuditService + audit_logs

SEGURIDAD
Sanctum + Policies + RBAC + Scope
```

---

# 112. Regla de reutilización

Antes de crear una nueva vista o componente:

```text
¿Ya existe un componente reutilizable?
```

Si existe:

```text
REUTILIZAR
```

Si no existe:

```text
CREAR COMPONENTE
```

No duplicar:

- Tablas.
- Modales.
- Formularios.
- Botones.
- Alertas.
- Filtros.
- Paginadores.
- Cards.

---

# 113. Regla del sistema visual

Ningún módulo podrá definir arbitrariamente:

```text
colores
tipografías
espaciados
botones
inputs
modales
```

Todo debe utilizar:

```text
Design Tokens
+
Componentes globales
```

Esto permitirá cambiar la apariencia completa desde una sola configuración.

---

# 114. Resultado final esperado

La plataforma será:

- Multiescuela.
- Multisede.
- Multirol.
- Multiusuario.
- Académica.
- Administrativa.
- Financiera.
- Documental.
- Auditable.
- Georreferenciada cuando corresponda.
- Escalable.
- Modular.
- Reutilizable.
- Preparada para procesamiento masivo.
- Preparada para aplicaciones móviles futuras.

---

# 115. Principio final

> **Laravel será el núcleo de autoridad del sistema. Blade + Livewire + Alpine.js proporcionarán el panel administrativo dinámico sin la complejidad de una SPA. Tailwind y los componentes reutilizables garantizarán una interfaz consistente. Redis manejará cache y procesamiento asíncrono. Python se utilizará exclusivamente para procesamiento pesado, análisis, optimización y operaciones masivas. MySQL conservará la información oficial. RBAC, Policies, alcance por sede y auditoría permitirán controlar exactamente quién puede hacer qué y reconstruir las operaciones críticas.**

El sistema deberá diseñarse desde el primer día para que una modificación posterior no destruya el historial académico, financiero, administrativo o de seguridad.
