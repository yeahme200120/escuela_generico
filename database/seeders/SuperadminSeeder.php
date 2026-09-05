<?php

namespace Database\Seeders;

use App\Models\Organizacion;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Sede;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserSede;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener organización y sede de referencia (deben existir)
        $org  = Organizacion::where('clave', 'DEMO')->firstOrFail();
        $sede = Sede::where('clave', 'SEDE-NORTE')->firstOrFail();

        // ── Usuarios por rol ──────────────────────────────────────────
        $usuarios = [
            [
                'email'    => 'superadmin@sistema.mx',
                'username' => 'superadmin',
                'nombres'  => 'Super',
                'apellido_paterno' => 'Admin',
                'apellido_materno' => 'Sistema',
                'rol_slug' => 'superadmin',
            ],
            [
                'email'    => 'directivo@sistema.mx',
                'username' => 'directivo',
                'nombres'  => 'Juan',
                'apellido_paterno' => 'Directivo',
                'apellido_materno' => 'Demo',
                'rol_slug' => 'directivo',
            ],
            [
                'email'    => 'administrador@sistema.mx',
                'username' => 'administrador',
                'nombres'  => 'Ana',
                'apellido_paterno' => 'Administrador',
                'apellido_materno' => 'Sistema',
                'rol_slug' => 'administrador',
            ],
            [
                'email'    => 'control_escolar@sistema.mx',
                'username' => 'control_escolar',
                'nombres'  => 'Luis',
                'apellido_paterno' => 'Control',
                'apellido_materno' => 'Escolar',
                'rol_slug' => 'control_escolar',
            ],
            [
                'email'    => 'docente@sistema.mx',
                'username' => 'docente',
                'nombres'  => 'María',
                'apellido_paterno' => 'Docente',
                'apellido_materno' => 'Demo',
                'rol_slug' => 'docente',
            ],
            [
                'email'    => 'cajero@sistema.mx',
                'username' => 'cajero',
                'nombres'  => 'Carlos',
                'apellido_paterno' => 'Cajero',
                'apellido_materno' => 'Demo',
                'rol_slug' => 'cajero',
            ],
            [
                'email'    => 'administrativo@sistema.mx',
                'username' => 'administrativo',
                'nombres'  => 'Laura',
                'apellido_paterno' => 'Admin',
                'apellido_materno' => 'Trativo',
                'rol_slug' => 'administrativo',
            ],
            [
                'email'    => 'rh@sistema.mx',
                'username' => 'rh',
                'nombres'  => 'Roberto',
                'apellido_paterno' => 'Recursos',
                'apellido_materno' => 'Humanos',
                'rol_slug' => 'rh',
            ],
            [
                'email'    => 'soporte@sistema.mx',
                'username' => 'soporte',
                'nombres'  => 'Sofía',
                'apellido_paterno' => 'Soporte',
                'apellido_materno' => 'Técnico',
                'rol_slug' => 'soporte',
            ],
            [
                'email'    => 'alumno@sistema.mx',
                'username' => 'alumno',
                'nombres'  => 'Pedro',
                'apellido_paterno' => 'Alumno',
                'apellido_materno' => 'Demo',
                'rol_slug' => 'alumno',
            ],
            [
                'email'    => 'tutor@sistema.mx',
                'username' => 'tutor',
                'nombres'  => 'María',
                'apellido_paterno' => 'Tutor',
                'apellido_materno' => 'Padre',
                'rol_slug' => 'tutor',
            ],
        ];

        foreach ($usuarios as $data) {
            $rol = Role::where('slug', $data['rol_slug'])->firstOrFail();

            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'organizacion_id'   => $org->id,
                    'nombres'           => $data['nombres'],
                    'apellido_paterno'  => $data['apellido_paterno'],
                    'apellido_materno'  => $data['apellido_materno'],
                    'username'          => $data['username'],
                    'password'          => Hash::make('Admin@2026!'),
                    'activo'            => true,
                    'email_verified_at' => now(),
                    'tema_preferido'    => 'light',
                    'locale'            => 'es',
                    'zona_horaria'      => 'America/Mexico_City',
                ]
            );

            // Asignar rol y sede
            $this->asignarRolYSede($user, $rol, $sede, $org->id);
        }

        $this->command->info('✅ Usuarios creados con contraseña: Admin@2026!');
        $this->command->table(
            ['Email', 'Username', 'Rol'],
            array_map(fn($u) => [$u['email'], $u['username'], $u['rol_slug']], $usuarios)
        );
    }

    private function asignarRolYSede(User $user, Role $rol, Sede $sede, int $orgId): void
    {
        // Asignar rol global en la organización (sin sede específica)
        UserRole::firstOrCreate(
            ['user_id' => $user->id, 'role_id' => $rol->id, 'sede_id' => null],
            ['escuela_id' => null, 'activo' => true]
        );

        // Asignar sede principal (para usuarios que requieren acceso a una sede)
        // Nota: Para roles como superadmin, no es necesario pero se asigna igual
        UserSede::firstOrCreate(
            ['user_id' => $user->id, 'sede_id' => $sede->id],
            ['es_principal' => true, 'activo' => true]
        );
    }
}