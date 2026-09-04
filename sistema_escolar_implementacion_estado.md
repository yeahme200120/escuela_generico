# Sistema Escolar - Estado de Implementaci�n (instrucciones operativas)

�ltima actualizaci�n: 2026-09-04 13:47 (Zona local)

Resumen: los m�dulos principales (Controllers, Vistas, Services core, Workers) est�n implementados en c�digo. Quedan tareas operativas y de integraci�n para dejar el sistema funcional.

Checklist operativo (pasos para dejar el sistema funcionando):

1) Preparar entorno PHP
   - Instalar dependencias: `composer install` (Laravel 12, composer 2+)
   - Copiar .env y ajustar variables (DB, REDIS, MAIL, PYTHON_API_URL, PYTHON_API_SECRET)
   - Generar APP_KEY: `php artisan key:generate`
   - Crear enlace de storage: `php artisan storage:link`

2) Migraciones y pivotes faltantes
   - Revisar migraciones existentes y crear migrations para tablas pivot necesarias (password_history, notificacion_usuario, python_job_logs, exportacion_logs, reportes_generados, reportes_programados, disciplina_registros, alertas_riesgo)
   - Ejecutar: `php artisan migrate`

3) Instalar paquetes PHP requeridos
   - `composer require pragmarx/google2fa maatwebsite/excel guzzlehttp/guzzle` (y otros que el c�digo llame)
   - Crear Mailables (App\Mail\NotificacionMail) y vistas de correo

4) Frontend y assets
   - Bootstrap ya integrado (Blade + Bootstrap 5)
   - Ejecutar: `npm install && npm run build` si hay assets

5) Queue y procesos as�ncronos
   - Configurar QUEUE_CONNECTION=redis
   - Levantar worker: `php artisan queue:work --sleep=3 --tries=3` (o usar Horizon)
   - Asegurar Redis funcionando

6) Python workers
   - Ir a carpeta `python/`
   - `pip install -r requirements.txt` (prefer virtualenv)
   - Configurar `.env` (PYTHON envs ya en python/.env.example)
   - Ejecutar servidor: `uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload`
   - Opcional: usar supervisor/systemd para background

7) Integraci�n Laravel ? Python
   - Ajustar `config/services.php` con `python.url` y `python.secret`
   - Probar health: `curl -H "X-Python-Secret: <secret>" http://localhost:8001/health`
   - Probar dispatch sync/async usando PythonJobService

8) Pruebas
   - PHPUnit: `vendor/bin/phpunit --filter CriticalControllers` (ejemplo)
   - Pytest: desde carpeta `python/` ejecutar `pytest -q`

9) Seguridad y producci�n
   - Asegurar `PYTHON_API_SECRET` fuerte y almacenado en secreto
   - Forzar HTTPS y configurar CORS si aplica
   - Configurar backups y monitorizaci�n

Notas puntuales para desarrolladores:
- PasswordPolicyService: corregir funci�n calcularFuerza si se reintroduce. Ya hay implementaci�n parcial.
- TwoFactorService requiere instalar `pragmarx/google2fa` y configurar SMS provider (Twilio / Otro).
- NotificacionService necesita Mailable y configuraci�n de mailer (Mailgun/Postmark/Ses).

Tareas pendientes (operativas):
- composer install (?)
- migraciones pivots y logs (?)
- mailables (pendiente)
- instalar paquetes php faltantes (pendiente)
- instalar dependencias python (parcialmente resuelto: imagen Docker construida)
- [x] Iniciar queue workers y uvicorn (arrancado localmente; queue worker PID: 19720)
- ejecutar tests (pendiente)
- [ ] Instalar dependencias PHP y Python
- [ ] Crear migraciones faltantes (pivots/logs)
- [ ] Generar mailables y plantillas de email
- [ ] Iniciar queue workers y uvicorn
- [ ] Ejecutar tests PHP y Python


Nota: la instalaci�n de pandas en Windows puede requerir Visual Studio Build Tools y Cython >= 3.0. En servidores Linux, use ruedas precompiladas: apt-get install -y build-essential python3-dev libatlas-base-dev; o usar contenedores Docker.

## Dockerización (nuevo)
Se han agregado archivos Docker para ejecutar los Python workers en contenedor Linux y evitar compilación nativa en Windows.
Archivos creados:
- docker-compose.yml
- python/Dockerfile
- python/requirements.txt (ajustado)

Estado actual:
- Imagen python-workers construida localmente (pinned openpyxl y PyJWT corregidos).
- Contenedores levantados con docker-compose; MySQL mapeado en el host al puerto 3310 (host:3310 -> container:3306) para evitar conflicto con MySQL local.
- Endpoint de salud verificado: `GET http://127.0.0.1:8001/health` responde con `{'status':'ok'}` si se envía `X-Python-Secret: dev-secret-key`.

Para (re)construir y levantar:
1. Asegurarse de tener Docker y docker-compose instalados
2. Ejecutar: `docker-compose build --no-cache` y luego `docker-compose up -d`
3. Ver logs: `docker-compose logs -f python-workers` o `docker-compose logs --tail 200 python-workers`

Notas:
- Se corrigieron versiones en `python/requirements.txt` (openpyxl y PyJWT) para evitar fallos de instalación en Linux containers.
- Las imágenes se construyen localmente y no se han subido a ningún registro público.

