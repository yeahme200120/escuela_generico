<?php

namespace Database\Seeders;

use App\Models\Aula;
use App\Models\CicloEscolar;
use App\Models\Edificio;
use App\Models\Escuela;
use App\Models\Grado;
use App\Models\NivelEducativo;
use App\Models\Organizacion;
use App\Models\Sede;
use Illuminate\Database\Seeder;

class OrganizacionSeeder extends Seeder
{
    public function run(): void
    {
        // ── Organización ─────────────────────────────────────────────
        $org = Organizacion::firstOrCreate(
            ['clave' => 'DEMO'],
            [
                'nombre'         => 'Institución Educativa Demo',
                'razon_social'   => 'Institución Educativa Demo S.C.',
                'rfc'            => 'IED000101AAA',
                'email'          => 'contacto@escuela.demo',
                'telefono'       => '5512345678',
                'ciudad'         => 'Ciudad de México',
                'estado'         => 'CDMX',
                'pais'           => 'México',
                'activa'         => true,
                'modulo_finanzas_activo'   => true,
                'modulo_rh_activo'         => true,
                'modulo_inventario_activo' => true,
            ]
        );

        // ── Escuela ───────────────────────────────────────────────────
        $escuela = Escuela::firstOrCreate(
            ['clave' => 'ESC-DEMO-01', 'organizacion_id' => $org->id],
            [
                'nombre'            => 'Colegio Demo',
                'clave_sep'         => 'SEP0000001',
                'tipo_sostenimiento'=> 'privado',
                'nivel_sistema'     => 'basica',
                'email'             => 'colegio@escuela.demo',
                'ciudad'            => 'Ciudad de México',
                'estado'            => 'CDMX',
                'activa'            => true,
            ]
        );

        // ── Sede principal ────────────────────────────────────────────
        $sede = Sede::firstOrCreate(
            ['clave' => 'SEDE-NORTE', 'escuela_id' => $escuela->id],
            [
                'organizacion_id'         => $org->id,
                'nombre'                  => 'Sede Norte',
                'email'                   => 'norte@escuela.demo',
                'telefono'                => '5587654321',
                'direccion'               => 'Av. Insurgentes Norte 1000',
                'ciudad'                  => 'Ciudad de México',
                'estado'                  => 'CDMX',
                'codigo_postal'           => '07000',
                'latitud'                 => 19.4978,
                'longitud'                => -99.1269,
                'radio_geocerca_metros'   => 500,
                'geocerca_activa'         => false,
                'calificacion_minima'     => 6.00,
                'calificacion_maxima'     => 10.00,
                'tolerancia_retardo_minutos' => 10,
                'zona_horaria'            => 'America/Mexico_City',
                'moneda'                  => 'MXN',
                'activa'                  => true,
            ]
        );

        // ── Edificio y aulas ──────────────────────────────────────────
        $edificio = Edificio::firstOrCreate(
            ['sede_id' => $sede->id, 'clave' => 'ED-A'],
            ['nombre' => 'Edificio A', 'numero_pisos' => 2, 'activo' => true]
        );

        foreach (['Aula 101', 'Aula 102', 'Aula 201', 'Laboratorio Cómputo'] as $i => $nombre) {
            Aula::firstOrCreate(
                ['sede_id' => $sede->id, 'nombre' => $nombre],
                [
                    'edificio_id'    => $edificio->id,
                    'tipo'           => str_contains($nombre, 'Lab') ? 'laboratorio' : 'salon',
                    'capacidad'      => 35,
                    'piso'           => $i < 2 ? 1 : 2,
                    'tiene_proyector'=> true,
                    'activa'         => true,
                ]
            );
        }

        // ── Niveles, grados ───────────────────────────────────────────
        $secundaria = NivelEducativo::firstOrCreate(
            ['escuela_id' => $escuela->id, 'clave' => 'SEC'],
            ['nombre' => 'Secundaria', 'orden' => 1, 'duracion_anos' => 3, 'activo' => true]
        );

        foreach (['1° Secundaria', '2° Secundaria', '3° Secundaria'] as $i => $nombre) {
            Grado::firstOrCreate(
                ['nivel_educativo_id' => $secundaria->id, 'nombre' => $nombre],
                ['orden' => $i + 1, 'activo' => true]
            );
        }

        // ── Ciclo escolar actual ──────────────────────────────────────
        CicloEscolar::firstOrCreate(
            ['escuela_id' => $escuela->id, 'nombre' => '2026-2027'],
            [
                'organizacion_id' => $org->id,
                'clave'           => '2026-27',
                'fecha_inicio'    => '2026-09-01',
                'fecha_fin'       => '2027-07-15',
                'estatus'         => 'activo',
                'es_actual'       => true,
            ]
        );
    }
}
