<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesPermisosSeeder::class,   // 1. Roles y permisos base (sin org)
            OrganizacionSeeder::class,    // 2. Organización demo + sedes + estructura
            SuperadminSeeder::class,      // 3. Usuarios con roles asignados
            MateriaSeeder::class,         // 4. Materias de la escuela demo
        ]);
    }
}
