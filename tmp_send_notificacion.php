<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(\App\Services\NotificacionService::class);
$user = \App\Models\User::first();
if (!$user) {
    // Insert usuario compatible con el esquema actual
    $id = \DB::table('users')->insertGetId([
        'uuid' => (string) \Str::uuid(),
        'nombres' => 'Test',
        'apellido_paterno' => 'Usuario',
        'apellido_materno' => null,
        'email' => 'test@example.com',
        'username' => 'testuser',
        'password' => bcrypt('secret'),
        'activo' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    $user = \App\Models\User::find($id);
}
$notif = $service->enviarMulticanal('Prueba Mailhog','Este es un correo de prueba desde Mailhog', [$user->id]);
echo "Notif ID: {$notif->id}\n";
