<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesPermisosSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. PERMISOS ──────────────────────────────────────────────
        $permisos = [
            // Auditoría
            ['modulo' => 'auditoria',       'accion' => 'ver',      'slug' => 'auditoria.ver',                   'nombre' => 'Ver auditoría',                   'alcance_default' => 'organizacion'],

            // Configuración
            ['modulo' => 'configuracion',   'accion' => 'ver',      'slug' => 'configuracion.ver',               'nombre' => 'Ver configuración',               'alcance_default' => 'organizacion'],
            ['modulo' => 'configuracion',   'accion' => 'editar',   'slug' => 'configuracion.apariencia.editar', 'nombre' => 'Editar apariencia',               'alcance_default' => 'global'],

            // Usuarios
            ['modulo' => 'usuarios',        'accion' => 'ver',      'slug' => 'usuarios.ver',                    'nombre' => 'Ver usuarios',                    'alcance_default' => 'organizacion'],
            ['modulo' => 'usuarios',        'accion' => 'crear',    'slug' => 'usuarios.crear',                  'nombre' => 'Crear usuarios',                  'alcance_default' => 'organizacion'],
            ['modulo' => 'usuarios',        'accion' => 'editar',   'slug' => 'usuarios.editar',                 'nombre' => 'Editar usuarios',                 'alcance_default' => 'organizacion'],
            ['modulo' => 'usuarios',        'accion' => 'eliminar', 'slug' => 'usuarios.eliminar',               'nombre' => 'Eliminar usuarios',               'alcance_default' => 'organizacion'],

            // Roles y permisos
            ['modulo' => 'roles',           'accion' => 'ver',      'slug' => 'roles.ver',                       'nombre' => 'Ver roles',                       'alcance_default' => 'organizacion'],
            ['modulo' => 'roles',           'accion' => 'asignar',  'slug' => 'roles.asignar',                   'nombre' => 'Asignar roles',                   'alcance_default' => 'organizacion'],

            // Sedes / escuelas
            ['modulo' => 'sedes',           'accion' => 'ver',      'slug' => 'sedes.ver',                       'nombre' => 'Ver sedes',                       'alcance_default' => 'escuela'],
            ['modulo' => 'sedes',           'accion' => 'crear',    'slug' => 'sedes.crear',                     'nombre' => 'Crear sedes',                     'alcance_default' => 'escuela'],
            ['modulo' => 'sedes',           'accion' => 'editar',   'slug' => 'sedes.editar',                    'nombre' => 'Editar sedes',                    'alcance_default' => 'escuela'],

            // Alumnos
            ['modulo' => 'alumnos',         'accion' => 'ver',      'slug' => 'alumnos.ver',                     'nombre' => 'Ver alumnos',                     'alcance_default' => 'sede'],
            ['modulo' => 'alumnos',         'accion' => 'crear',    'slug' => 'alumnos.crear',                   'nombre' => 'Crear alumnos',                   'alcance_default' => 'sede'],
            ['modulo' => 'alumnos',         'accion' => 'editar',   'slug' => 'alumnos.editar',                  'nombre' => 'Editar alumnos',                  'alcance_default' => 'sede'],
            ['modulo' => 'alumnos',         'accion' => 'eliminar', 'slug' => 'alumnos.eliminar',                'nombre' => 'Eliminar alumnos',                'alcance_default' => 'sede'],
            ['modulo' => 'alumnos',         'accion' => 'exportar', 'slug' => 'alumnos.exportar',                'nombre' => 'Exportar alumnos',                'alcance_default' => 'sede'],

            // Docentes
            ['modulo' => 'docentes',        'accion' => 'ver',      'slug' => 'docentes.ver',                    'nombre' => 'Ver docentes',                    'alcance_default' => 'sede'],
            ['modulo' => 'docentes',        'accion' => 'crear',    'slug' => 'docentes.crear',                  'nombre' => 'Crear docentes',                  'alcance_default' => 'sede'],
            ['modulo' => 'docentes',        'accion' => 'editar',   'slug' => 'docentes.editar',                 'nombre' => 'Editar docentes',                 'alcance_default' => 'sede'],

            // Grupos
            ['modulo' => 'grupos',          'accion' => 'ver',      'slug' => 'grupos.ver',                      'nombre' => 'Ver grupos',                      'alcance_default' => 'sede'],
            ['modulo' => 'grupos',          'accion' => 'crear',    'slug' => 'grupos.crear',                    'nombre' => 'Crear grupos',                    'alcance_default' => 'sede'],
            ['modulo' => 'grupos',          'accion' => 'editar',   'slug' => 'grupos.editar',                   'nombre' => 'Editar grupos',                   'alcance_default' => 'sede'],

            // Calificaciones
            ['modulo' => 'calificaciones',  'accion' => 'ver',      'slug' => 'calificaciones.ver',              'nombre' => 'Ver calificaciones',              'alcance_default' => 'grupo'],
            ['modulo' => 'calificaciones',  'accion' => 'registrar','slug' => 'calificaciones.registrar',        'nombre' => 'Registrar calificaciones',        'alcance_default' => 'grupo'],
            ['modulo' => 'calificaciones',  'accion' => 'editar',   'slug' => 'calificaciones.editar',           'nombre' => 'Editar calificaciones',           'alcance_default' => 'grupo'],
            ['modulo' => 'calificaciones',  'accion' => 'cerrar',   'slug' => 'calificaciones.cerrar',           'nombre' => 'Cerrar período calificaciones',   'alcance_default' => 'sede'],
            ['modulo' => 'calificaciones',  'accion' => 'autorizar','slug' => 'calificaciones.autorizar',        'nombre' => 'Autorizar calificaciones',        'alcance_default' => 'sede'],

            // Asistencias
            ['modulo' => 'asistencias',     'accion' => 'ver',      'slug' => 'asistencias.ver',                 'nombre' => 'Ver asistencias',                 'alcance_default' => 'grupo'],
            ['modulo' => 'asistencias',     'accion' => 'registrar','slug' => 'asistencias.registrar',           'nombre' => 'Registrar asistencias',           'alcance_default' => 'grupo'],
            ['modulo' => 'asistencias',     'accion' => 'editar',   'slug' => 'asistencias.editar',              'nombre' => 'Editar asistencias',              'alcance_default' => 'grupo'],

            // Horarios
            ['modulo' => 'horarios',        'accion' => 'ver',      'slug' => 'horarios.ver',                    'nombre' => 'Ver horarios',                    'alcance_default' => 'sede'],
            ['modulo' => 'horarios',        'accion' => 'crear',    'slug' => 'horarios.crear',                  'nombre' => 'Crear horarios',                  'alcance_default' => 'sede'],
            ['modulo' => 'horarios',        'accion' => 'editar',   'slug' => 'horarios.editar',                 'nombre' => 'Editar horarios',                 'alcance_default' => 'sede'],
            ['modulo' => 'horarios',        'accion' => 'publicar', 'slug' => 'horarios.publicar',               'nombre' => 'Publicar horarios',               'alcance_default' => 'sede'],

            // Pagos / Finanzas
            ['modulo' => 'pagos',           'accion' => 'ver',      'slug' => 'pagos.ver',                       'nombre' => 'Ver pagos',                       'alcance_default' => 'sede'],
            ['modulo' => 'pagos',           'accion' => 'registrar','slug' => 'pagos.registrar',                 'nombre' => 'Registrar pagos',                 'alcance_default' => 'sede'],
            ['modulo' => 'pagos',           'accion' => 'cancelar', 'slug' => 'pagos.cancelar',                  'nombre' => 'Cancelar pagos',                  'alcance_default' => 'sede'],
            ['modulo' => 'pagos',           'accion' => 'devolver', 'slug' => 'pagos.devolver',                  'nombre' => 'Devolver pagos',                  'alcance_default' => 'sede'],

            // Caja
            ['modulo' => 'caja',            'accion' => 'ver',      'slug' => 'caja.ver',                        'nombre' => 'Ver caja',                        'alcance_default' => 'sede'],
            ['modulo' => 'caja',            'accion' => 'abrir',    'slug' => 'caja.abrir',                      'nombre' => 'Abrir caja',                      'alcance_default' => 'sede'],
            ['modulo' => 'caja',            'accion' => 'cerrar',   'slug' => 'caja.cerrar',                     'nombre' => 'Cerrar caja',                     'alcance_default' => 'sede'],

            // Control escolar
            ['modulo' => 'control_escolar', 'accion' => 'ver',      'slug' => 'control_escolar.ver',             'nombre' => 'Ver control escolar',             'alcance_default' => 'sede'],
            ['modulo' => 'control_escolar', 'accion' => 'inscribir','slug' => 'control_escolar.inscribir',       'nombre' => 'Inscribir alumnos',               'alcance_default' => 'sede'],
            ['modulo' => 'control_escolar', 'accion' => 'bajas',    'slug' => 'control_escolar.bajas',           'nombre' => 'Gestionar bajas',                 'alcance_default' => 'sede'],

            // Documentos
            ['modulo' => 'documentos',      'accion' => 'ver',      'slug' => 'documentos.ver',                  'nombre' => 'Ver documentos',                  'alcance_default' => 'sede'],
            ['modulo' => 'documentos',      'accion' => 'generar',  'slug' => 'documentos.generar',              'nombre' => 'Generar documentos',              'alcance_default' => 'sede'],
            ['modulo' => 'documentos',      'accion' => 'autorizar','slug' => 'documentos.autorizar',            'nombre' => 'Autorizar documentos',            'alcance_default' => 'sede'],

            // Reportes
            ['modulo' => 'reportes',        'accion' => 'ver',      'slug' => 'reportes.ver',                    'nombre' => 'Ver reportes',                    'alcance_default' => 'sede'],
            ['modulo' => 'reportes',        'accion' => 'exportar', 'slug' => 'reportes.exportar',               'nombre' => 'Exportar reportes',               'alcance_default' => 'sede'],

            // Inventario
            ['modulo' => 'inventario',      'accion' => 'ver',      'slug' => 'inventario.ver',                  'nombre' => 'Ver inventario',                  'alcance_default' => 'sede'],
            ['modulo' => 'inventario',      'accion' => 'gestionar','slug' => 'inventario.gestionar',            'nombre' => 'Gestionar inventario',            'alcance_default' => 'sede'],

            // RH
            ['modulo' => 'rh',              'accion' => 'ver',      'slug' => 'rh.ver',                          'nombre' => 'Ver RH',                          'alcance_default' => 'organizacion'],
            ['modulo' => 'rh',              'accion' => 'gestionar','slug' => 'rh.gestionar',                    'nombre' => 'Gestionar RH',                    'alcance_default' => 'organizacion'],
        ];

        foreach ($permisos as $p) {
            Permission::firstOrCreate(
                ['slug' => $p['slug']],
                [
                    'nombre'         => $p['nombre'],
                    'modulo'         => $p['modulo'],
                    'accion'         => $p['accion'],
                    'alcance_default'=> $p['alcance_default'],
                    'activo'         => true,
                ]
            );
        }

        // ── 2. ROLES ─────────────────────────────────────────────────
        $roles = [
            ['slug' => 'superadmin',       'nombre' => 'Superadministrador', 'nivel' => 1,  'es_sistema' => true],
            ['slug' => 'directivo',        'nombre' => 'Directivo',          'nivel' => 10, 'es_sistema' => true],
            ['slug' => 'administrador',    'nombre' => 'Administrador',      'nivel' => 20, 'es_sistema' => true],
            ['slug' => 'control_escolar',  'nombre' => 'Control Escolar',    'nivel' => 30, 'es_sistema' => true],
            ['slug' => 'docente',          'nombre' => 'Docente',            'nivel' => 40, 'es_sistema' => true],
            ['slug' => 'cajero',           'nombre' => 'Cajero',             'nivel' => 40, 'es_sistema' => true],
            ['slug' => 'administrativo',   'nombre' => 'Administrativo',     'nivel' => 50, 'es_sistema' => true],
            ['slug' => 'rh',               'nombre' => 'Recursos Humanos',   'nivel' => 50, 'es_sistema' => true],
            ['slug' => 'alumno',           'nombre' => 'Alumno',             'nivel' => 80, 'es_sistema' => true],
            ['slug' => 'tutor',            'nombre' => 'Tutor/Padre',        'nivel' => 85, 'es_sistema' => true],
            ['slug' => 'soporte',          'nombre' => 'Soporte técnico',    'nivel' => 5,  'es_sistema' => true],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(
                ['slug' => $r['slug']],
                [
                    'nombre'      => $r['nombre'],
                    'nivel'       => $r['nivel'],
                    'es_sistema'  => $r['es_sistema'],
                    'activo'      => true,
                ]
            );
        }

        // ── 3. ASIGNAR PERMISOS A ROLES ──────────────────────────────
        $this->asignarPermisos();
    }

    private function asignarPermisos(): void
    {
        // Superadmin — todos los permisos (se maneja en la Policy directamente)
        // Los demás roles reciben sus permisos específicos:

        $mapa = [
            'directivo' => [
                'auditoria.ver', 'usuarios.ver',
                'alumnos.ver', 'alumnos.exportar',
                'docentes.ver', 'grupos.ver',
                'calificaciones.ver', 'calificaciones.autorizar',
                'asistencias.ver', 'horarios.ver',
                'pagos.ver', 'caja.ver',
                'control_escolar.ver', 'documentos.ver', 'documentos.autorizar',
                'reportes.ver', 'reportes.exportar', 'sedes.ver',
            ],
            'administrador' => [
                'usuarios.ver', 'usuarios.crear', 'usuarios.editar',
                'roles.ver', 'roles.asignar',
                'sedes.ver', 'sedes.editar',
                'alumnos.ver', 'alumnos.crear', 'alumnos.editar', 'alumnos.exportar',
                'docentes.ver', 'docentes.crear', 'docentes.editar',
                'grupos.ver', 'grupos.crear', 'grupos.editar',
                'pagos.ver', 'pagos.registrar', 'pagos.cancelar',
                'caja.ver', 'caja.abrir', 'caja.cerrar',
                'control_escolar.ver', 'control_escolar.inscribir', 'control_escolar.bajas',
                'documentos.ver', 'documentos.generar', 'documentos.autorizar',
                'reportes.ver', 'reportes.exportar',
                'inventario.ver', 'inventario.gestionar',
            ],
            'control_escolar' => [
                'alumnos.ver', 'alumnos.crear', 'alumnos.editar', 'alumnos.exportar',
                'docentes.ver',
                'grupos.ver', 'grupos.editar',
                'calificaciones.ver', 'calificaciones.editar', 'calificaciones.cerrar',
                'asistencias.ver', 'asistencias.editar',
                'horarios.ver',
                'control_escolar.ver', 'control_escolar.inscribir', 'control_escolar.bajas',
                'documentos.ver', 'documentos.generar', 'documentos.autorizar',
                'reportes.ver', 'reportes.exportar',
            ],
            'docente' => [
                'alumnos.ver',
                'grupos.ver',
                'calificaciones.ver', 'calificaciones.registrar', 'calificaciones.editar',
                'asistencias.ver', 'asistencias.registrar', 'asistencias.editar',
                'horarios.ver',
                'documentos.ver',
            ],
            'cajero' => [
                'alumnos.ver',
                'pagos.ver', 'pagos.registrar',
                'caja.ver', 'caja.abrir', 'caja.cerrar',
                'reportes.ver',
            ],
            'administrativo' => [
                'alumnos.ver',
                'documentos.ver', 'documentos.generar',
                'reportes.ver',
                'inventario.ver',
            ],
            'rh' => [
                'usuarios.ver',
                'rh.ver', 'rh.gestionar',
                'reportes.ver',
            ],
            'soporte' => [
                'auditoria.ver',
                'usuarios.ver', 'usuarios.editar',
                'configuracion.ver',
            ],
            'alumno'  => [],
            'tutor'   => [],
        ];

        foreach ($mapa as $roleSlug => $permSlugs) {
            $role = Role::where('slug', $roleSlug)->first();
            if (!$role) continue;

            $permIds = Permission::whereIn('slug', $permSlugs)->pluck('id')->toArray();

            // syncWithoutDetaching para ser idempotente
            $syncData = [];
            foreach ($permIds as $id) {
                $syncData[$id] = ['alcance' => 'sede'];
            }
            $role->permissions()->syncWithoutDetaching($syncData);
        }
    }
}
