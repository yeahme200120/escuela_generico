<?php

namespace Database\Factories;

use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estudiante>
 */
class EstudianteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombres' => $this->faker->firstName(),
            'apellido_paterno' => $this->faker->lastName(),
            'apellido_materno' => $this->faker->lastName(),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-20 years', '-8 years')->format('Y-m-d'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->phoneNumber(),
            'matricula' => 'EST-' . $this->faker->unique()->numberBetween(10000, 99999),
            'curp' => strtoupper($this->faker->bothify('??????????######')),
            'direccion' => $this->faker->address(),
            'estatus' => 'activo',
            'situacion_academica' => 'regular',
            'situacion_inscripcion' => 'inscrito',
            'estatus_riesgo' => 'normal',
        ];
    }
}
