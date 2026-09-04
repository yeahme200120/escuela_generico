<?php

namespace Database\Seeders;

use App\Models\Escuela;
use App\Models\Materia;
use Illuminate\Database\Seeder;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        $escuela = Escuela::where('clave', 'ESC-DEMO-01')->first();

        if (! $escuela) {
            $this->command->warn('MateriaSeeder: No se encontró la escuela con clave ESC-DEMO-01. Se omite la siembra.');
            return;
        }

        $materias = [
            [
                'nombre'      => 'Español',
                'tipo'        => 'obligatoria',
                'horas_semana' => 5,
                'creditos'    => 8,
            ],
            [
                'nombre'      => 'Matemáticas',
                'tipo'        => 'obligatoria',
                'horas_semana' => 5,
                'creditos'    => 8,
            ],
            [
                'nombre'      => 'Historia',
                'tipo'        => 'obligatoria',
                'horas_semana' => 3,
                'creditos'    => 5,
            ],
            [
                'nombre'      => 'Geografía',
                'tipo'        => 'obligatoria',
                'horas_semana' => 3,
                'creditos'    => 5,
            ],
            [
                'nombre'      => 'Ciencias Naturales',
                'tipo'        => 'obligatoria',
                'horas_semana' => 4,
                'creditos'    => 6,
            ],
            [
                'nombre'      => 'Formación Cívica y Ética',
                'tipo'        => 'obligatoria',
                'horas_semana' => 2,
                'creditos'    => 4,
            ],
            [
                'nombre'      => 'Educación Física',
                'tipo'        => 'taller',
                'horas_semana' => 2,
                'creditos'    => 3,
            ],
            [
                'nombre'      => 'Artes',
                'tipo'        => 'optativa',
                'horas_semana' => 2,
                'creditos'    => 3,
            ],
            [
                'nombre'      => 'Tecnología',
                'tipo'        => 'taller',
                'horas_semana' => 2,
                'creditos'    => 3,
            ],
            [
                'nombre'      => 'Inglés',
                'tipo'        => 'obligatoria',
                'horas_semana' => 3,
                'creditos'    => 5,
            ],
        ];

        foreach ($materias as $datos) {
            Materia::firstOrCreate(
                [
                    'escuela_id' => $escuela->id,
                    'nombre'     => $datos['nombre'],
                ],
                array_merge($datos, [
                    'escuela_id' => $escuela->id,
                    'activa'     => true,
                ])
            );
        }

        $this->command->info("MateriaSeeder: " . count($materias) . " materias procesadas para la escuela '{$escuela->nombre}'.");
    }
}
