# Sistema Escolar - Python Workers

Microservicio asincrónico para procesamiento de tareas pesadas en FastAPI.

## Instalación

```bash
cd python
pip install -r requirements.txt
cp .env.example .env
```

## Ejecutar

```bash
cd python
python -m uvicorn app.main:app --reload --host 0.0.0.0 --port 8001
```

## API Endpoints

- **GET /health** — Estado del servicio
- **GET /workers/status** — Estado de workers disponibles
- **POST /workers/calcular-indicadores** — Calcular indicadores académicos
- **POST /workers/calcular-riesgo** — Calcular riesgo académico
- **POST /workers/generar-reportes** — Generar reportes masivos
- **POST /workers/procesar-importaciones** — Procesar importaciones Excel/CSV

## Workers

### calcular_indicadores
Calcula tasas de aprobación, deserción y permanencia por sede/ciclo.

### calcular_riesgo
Evalúa riesgo académico de estudiantes según criterios (calificaciones, asistencias, etc).

### generar_reportes
Genera reportes masivos en Excel/PDF con filtros personalizados.

### procesar_importaciones
Procesa importaciones de datos desde archivos Excel/CSV.

## Desarrollo

```bash
# Crear test
python -m pytest tests/

# Lint
pylint app/
```

## Integración Laravel

Las tareas se despachan desde Laravel Queue:

```php
dispatch(new \App\Jobs\DispatchPythonJob('calcular-indicadores', ['sede_id' => 1]));
```
