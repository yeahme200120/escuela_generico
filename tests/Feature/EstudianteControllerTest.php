<?php

namespace Tests\Feature;

use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EstudianteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Crear un usuario autenticado para los tests
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * Test listing estudiantes
     */
    public function test_puede_listar_estudiantes()
    {
        Estudiante::factory()->count(3)->create();

        $response = $this->get(route('estudiantes.index'));

        $response->assertStatus(200);
        $response->assertViewHas('estudiantes');
    }

    /**
     * Test create form
     */
    public function test_puede_ver_formulario_crear_estudiante()
    {
        $response = $this->get(route('estudiantes.create'));

        $response->assertStatus(200);
        $response->assertViewHas('organizaciones');
        $response->assertViewHas('sedes');
    }

    /**
     * Test store estudiante
     */
    public function test_puede_crear_estudiante()
    {
        $data = [
            'nombres' => 'Juan',
            'apellido_paterno' => 'Perez',
            'apellido_materno' => 'Martinez',
            'email' => 'juan@example.com',
            'estatus' => 'activo',
        ];

        $response = $this->post(route('estudiantes.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('estudiantes', [
            'nombres' => 'Juan',
            'apellido_paterno' => 'Perez',
        ]);
    }

    /**
     * Test show estudiante
     */
    public function test_puede_ver_detalles_estudiante()
    {
        $estudiante = Estudiante::factory()->create();

        $response = $this->get(route('estudiantes.show', $estudiante));

        $response->assertStatus(200);
        $response->assertViewHas('estudiante');
        $response->assertSee($estudiante->nombres);
    }

    /**
     * Test edit form
     */
    public function test_puede_ver_formulario_editar_estudiante()
    {
        $estudiante = Estudiante::factory()->create();

        $response = $this->get(route('estudiantes.edit', $estudiante));

        $response->assertStatus(200);
        $response->assertViewHas('estudiante');
    }

    /**
     * Test update estudiante
     */
    public function test_puede_actualizar_estudiante()
    {
        $estudiante = Estudiante::factory()->create();

        $data = [
            'nombres' => 'Juan Updated',
            'apellido_paterno' => 'Perez',
            'estatus' => 'baja_temporal',
        ];

        $response = $this->put(route('estudiantes.update', $estudiante), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('estudiantes', [
            'id' => $estudiante->id,
            'nombres' => 'Juan Updated',
            'estatus' => 'baja_temporal',
        ]);
    }

    /**
     * Test delete estudiante
     */
    public function test_puede_eliminar_estudiante()
    {
        $estudiante = Estudiante::factory()->create();

        $response = $this->delete(route('estudiantes.destroy', $estudiante));

        $response->assertRedirect();
        $this->assertSoftDeleted('estudiantes', ['id' => $estudiante->id]);
    }
}
