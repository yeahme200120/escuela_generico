"""
Worker de importaciones masivas §77
Procesa Excel/CSV: alumnos, calificaciones, horarios
"""

import logging
import base64
import io

log = logging.getLogger("sistema_escolar.importaciones")


async def procesar(data: dict) -> dict:
    """
    Entrada:
        tipo:    'alumnos' | 'calificaciones' | 'horarios'
        archivo: contenido base64 del archivo
        formato: 'csv' | 'xlsx'
    """
    tipo    = data.get("tipo", "alumnos")
    archivo = data.get("archivo")  # base64
    formato = data.get("formato", "csv")

    log.info(f"Procesando importación tipo={tipo} formato={formato}")

    if not archivo:
        return {"procesados": 0, "errores": 0, "advertencias": ["Sin archivo recibido"]}

    try:
        import pandas as pd

        contenido = base64.b64decode(archivo)
        buf = io.BytesIO(contenido)

        if formato == "xlsx":
            df = pd.read_excel(buf)
        else:
            df = pd.read_csv(buf, encoding="utf-8-sig")

        # Validación básica según tipo
        errores = []
        advertencias = []

        if tipo == "alumnos":
            requeridos = ["nombres", "apellido_paterno"]
            for col in requeridos:
                if col not in df.columns:
                    errores.append(f"Columna requerida faltante: {col}")

        registros_validos = len(df) - len(df[df.isnull().any(axis=1)])

        return {
            "procesados":   registros_validos,
            "total":        len(df),
            "errores":      len(errores),
            "advertencias": errores + advertencias,
            "columnas":     list(df.columns),
        }

    except ImportError:
        return {"procesados": 0, "errores": 1, "advertencias": ["pandas no instalado"]}
    except Exception as e:
        log.error(f"Error procesando importación: {e}")
        return {"procesados": 0, "errores": 1, "advertencias": [str(e)]}
