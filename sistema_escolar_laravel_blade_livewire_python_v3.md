# Sistema Escolar - Implementación técnica (Guía rápida para llevar a producción)

Basado en: sistema_escolar_laravel_blade_livewire_python_v3.md

Resumen de cambios relevantes:
- Stack: Laravel 12 + Blade + Bootstrap 5 (Livewire no usado activamente en esta iteración).
- Backend: 50 controllers, 185+ vistas, 10 services (7 impl.), Python workers (FastAPI) scaffold y 4 workers implementados.

Cómo desplegar y verificar (comandos rápidos):

1) Preparar entorno local/servidor
   - `git pull && composer install && npm install && npm run build && cp .env.example .env && php artisan key:generate`

2) BD & migraciones
   - Crear base: `php artisan migrate`
   - Si faltan pivots: revisar carpeta `database/migrations/` y crear migraciones para `password_history, notificacion_usuario, python_job_logs, exportacion_logs, reportes_generados`.

3) Dependencias específicas
   - `composer require pragmarx/google2fa maatwebsite/excel`
   - Mail: configurar `MAIL_MAILER` y crear `App\Mail\NotificacionMail`

4) Python
   - `cd python && python -m venv .venv && source .venv/bin/activate && pip install -r requirements.txt`
   - `uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload`

5) Integración y prueba
   - Ajustar `config/services.php` (python.url, python.secret)
   - Probar: `php artisan tinker` → `(new App\Services\PythonJobService)->verificarSalud();`
   - Dispatch de prueba: `(new App\Services\PythonJobService)->calcularIndicadoresAsync(1,1);`

6) Verificación final
   - Revisar `python_job_logs` y `reportes_generados` para confirmar ejecuciones
   - Ejecutar tests: `vendor/bin/phpunit` y `cd python && pytest -q`

Detalles importantes:
- Bootstrap está correctamente integrado en las vistas Blade; revisar estilos globales en `resources/css`.
- Livewire fue reemplazado por componentes Blade + servicios; si requiere reintroducir Livewire, adaptar componentes y recursos JS.


## Dockerización añadida
Se añadió docker-compose.yml y python/Dockerfile para ejecutar servicios localmente:
- MySQL 8 (mapeado en el host al puerto 3310 para evitar conflictos con MySQL local)
- Redis
- Python workers (FastAPI)

Estado y verificación rápida:
1. Imagen `sistema-escolar-python-workers` construida localmente (se ajustaron versiones en `python/requirements.txt`).
2. Contenedores levantados con `docker-compose up -d` (MySQL -> host:3310, Redis -> 6379, Python -> 8001).
3. Health check: `curl -H "X-Python-Secret: dev-secret-key" http://127.0.0.1:8001/health` → devuelve `{"status":"ok"}`.
4. Queue worker: arrancado localmente (php artisan queue:work) — PID: 19720. Asegurar QUEUE_CONNECTION=redis en .env y que Redis esté accesible en 127.0.0.1:6379.

Ejecutar para reproducir:
- `docker-compose build --no-cache && docker-compose up -d`
- `docker-compose logs --tail 200 python-workers` para revisar arranque y errores.

