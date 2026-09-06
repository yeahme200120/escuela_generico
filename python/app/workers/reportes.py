"""
Worker de reportes masivos §78
Genera CSV/Excel y lo almacena en storage para descarga autorizada
"""

import logging
import os
import csv
import io

log = logging.getLogger("sistema_escolar.reportes")

STORAGE_PATH = os.path.join(os.path.dirname(__file__), "..", "..", "..", "storage", "app", "reportes")


async def generar(data: dict) -> dict:
    """
    Entrada:  tipo, filtros (ciclo_id, sede_id, fechas)
    Salida:   path del archivo generado
    """
    tipo    = data.get("tipo", "calificaciones")
    job_id  = data.get("job_id", "unknown")

    log.info(f"Generando reporte tipo={tipo} job={job_id}")

    os.makedirs(STORAGE_PATH, exist_ok=True)
    nombre  = f"{tipo}_{job_id}.csv"
    ruta    = os.path.join(STORAGE_PATH, nombre)
    ruta_rel= f"reportes/{nombre}"

    # TODO: consultar MySQL con los filtros y escribir datos reales
    output = io.StringIO()
    writer = csv.writer(output)
    writer.writerow(["ID", "Descripcion", "Valor"])
    writer.writerow([1, "Dato de ejemplo", "—"])

    with open(ruta, "w", encoding="utf-8-sig", newline="") as f:
        f.write(output.getvalue())

    return {
        "archivo":    ruta_rel,
        "registros":  1,
        "formato":    "csv",
        "nota":       "Worker pendiente de implementación completa"
    }
