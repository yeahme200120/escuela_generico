"""
Sistema Escolar — Python Worker (FastAPI)
§3, §76-§78, §109

El browser NUNCA se comunica directamente con este servicio.
Flujo: Laravel Job → Redis Queue → Python Worker → resultado → Laravel
"""

from fastapi import FastAPI, Request, HTTPException
from fastapi.responses import JSONResponse
import os
import logging
from dotenv import load_dotenv

load_dotenv()

# ── Configuración ────────────────────────────────────────────────────────
SECRET  = os.getenv("PYTHON_SERVICE_SECRET", "")
LOG_DIR = os.path.join(os.path.dirname(__file__), "..", "logs")
os.makedirs(LOG_DIR, exist_ok=True)

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.FileHandler(os.path.join(LOG_DIR, "python.log")),
        logging.StreamHandler()
    ]
)
log = logging.getLogger("sistema_escolar")

# ── App ──────────────────────────────────────────────────────────────────
app = FastAPI(
    title="Sistema Escolar — Python Worker",
    description="Workers de procesamiento pesado. Solo accesible desde Laravel.",
    version="1.0.0",
    docs_url="/docs" if os.getenv("PYTHON_DOCS", "false").lower() == "true" else None,
)

# ── Middleware de autenticación ──────────────────────────────────────────
@app.middleware("http")
async def auth_middleware(request: Request, call_next):
    public_paths = ["/", "/health", "/openapi.json"]
    if request.url.path not in public_paths:
        token = request.headers.get("X-Python-Secret", "")
        if not SECRET or token != SECRET:
            log.warning(f"Unauthorized request to {request.url.path}")
            raise HTTPException(status_code=401, detail="Unauthorized")
    response = await call_next(request)
    return response

# ── Health ───────────────────────────────────────────────────────────────
@app.get("/")
@app.get("/health")
async def health():
    return {"status": "ok", "service": "Sistema Escolar Python Worker", "version": "1.0.0"}

# ── Dispatcher principal §76 ─────────────────────────────────────────────
@app.post("/jobs/{tipo}")
async def ejecutar_job(tipo: str, data: dict, request: Request):
    """Punto de entrada para todos los jobs de Laravel."""
    from app.workers import estadisticas, riesgo, importaciones, reportes, horarios

    job_id = data.get("job_id", "unknown")
    log.info(f"Job recibido: tipo={tipo} job_id={job_id}")

    handlers = {
        "estadisticas":  estadisticas.calcular,
        "riesgo":        riesgo.calcular,
        "importacion":   importaciones.procesar,
        "reporte":       reportes.generar,
        "horario":       horarios.optimizar,
    }

    handler = handlers.get(tipo)
    if not handler:
        raise HTTPException(status_code=404, detail=f"Tipo de job no soportado: {tipo}")

    try:
        result = await handler(data)
        log.info(f"Job completado: {job_id}")
        return {"job_id": job_id, "status": "completed", "results": result}
    except Exception as e:
        log.error(f"Job fallido: {job_id} — {e}")
        raise HTTPException(status_code=500, detail=str(e))
