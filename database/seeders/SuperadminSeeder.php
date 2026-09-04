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
        $org  = Organizacion::where('clave', 'DEMO')->firstOrFail();
        $sede = Sede::where('clave', 'SEDE-NORTE')->firstOrFail();
        $rol  = Role::where('slug', 'superadmin')->firstOrFail();

        // ── Superadmin ────────────────────────────────────────────────
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@sistema.mx'],
            [
                'organizacion_id'  => $org->id,
                'nombres'          => 'Super',
                'apellido_paterno'  => 'Admin',
                'apellido_materno'  => 'Sistema',
                'username'         => 'superadmin',
                'password'         => Hash::make('Admin@2026!'),
                'activo'           => true,
                'email_verified_at'=> now(),
                'tema_preferido'   => 'light',
                'locale'           => 'es',
                'zona_horaria'     => 'America/Mexico_City',
            ]
        );

        $this->asignarRolYSede($superadmin, $rol, $sede, $org->id);

        // ── Usuario directivo de prueba ───────────────────────────────
        $rolDirectivo = Role::where('slug', 'directivo')->firstOrFail();

        $directivo = User::firstOrCreate(
            ['email' => 'directivo@sistema.mx'],
            [
                'organizacion_id'  => $org->id,
                'nombres'          => 'Juan',
                'apellido_paterno'  => 'Directivo',
                'apellido_materno'  => 'Demo',
                'username'         => 'directivo',
                'password'         => Hash::make('Admin@2026!'),
                'activo'           => true,
                'email_verified_at'=> now(),
            ]
        );

        $this->asignarRolYSede($directivo, $rolDirectivo, $sede, $org->id);

        // ── Usuario docente de prueba ─────────────────────────────────
        $rolDocente = Role::where('slug', 'docente')->firstOrFail();

        $docente = User::firstOrCreate(
            ['email' => 'docente@sistema.mx'],
            [
                'organizacion_id'  => $org->id,
                'nombres'          => 'María',
                'apellido_paterno'  => 'Docente',
                'apellido_materno'  => 'Demo',
                'username'         => 'docente',
                'password'         => Hash::make('Admin@2026!'),
                'activo'           => true,
                'email_verified_at'=> now(),
            ]
        );

        $this->asignarRolYSede($docente, $rolDocente, $sede, $org->id);

        // ── Usuario cajero de prueba ──────────────────────────────────
        $rolCajero = Role::where('slug', 'cajero')->firstOrFail();

        $cajero = User::firstOrCreate(
            ['email' => 'cajero@sistema.mx'],
            [
                'organizacion_id'  => $org->id,
                'nombres'          => 'Carlos',
                'apellido_paterno'  => 'Cajero',
                'apellido_materno'  => 'Demo',
                'username'         => 'cajero',
                'password'         => Hash::make('Admin@2026!'),
                'activo'           => true,
                'email_verified_at'=> now(),
            ]
        );

        $this->asignarRolYSede($cajero, $rolCajero, $sede, $org->id);

        $this->command->info('Usuarios creados:');
        $this->command->table(
            ['Email', 'Username', 'Rol', 'Password'],
            [
                ['superadmin@sistema.mx', 'superadmin', 'Superadministrador', 'Admin@2026!'],
                ['directivo@sistema.mx',  'directivo',  'Directivo',          'Admin@2026!'],
                ['docente@sistema.mx',    'docente',    'Docente',             'Admin@2026!'],
                ['cajero@sistema.mx',     'cajero',     'Cajero',              'Admin@2026!'],
            ]
        );
    }

    private function asignarRolYSede(User $user, Role $rol, Sede $sede, int $orgId): void
    {
        // Rol global en la organización
        UserRole::firstOrCreate(
            ['user_id' => $user->id, 'role_id' => $rol->id, 'sede_id' => null],
            ['escuela_id' => null, 'activo' => true]
        );

        // Sede principal
        UserSede::firstOrCreate(
            ['user_id' => $user->id, 'sede_id' => $sede->id],
            ['es_principal' => true, 'activo' => true]
        );
    }
}
