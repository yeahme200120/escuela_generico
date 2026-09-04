FROM python:3.11-slim

ENV PYTHONDONTWRITEBYTECODE 1
ENV PYTHONUNBUFFERED 1

# System deps for compiling dependencies like pandas
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    gcc \
    g++ \
    libpq-dev \
    libssl-dev \
    curl \
    ca-certificates \
    git \
    wget \
    nano \
    locales \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Create non-root user
RUN useradd -ms /bin/bash appuser

# Copy requirements first for better caching
COPY requirements.txt /app/requirements.txt

RUN pip install --upgrade pip setuptools wheel
RUN pip install -r /app/requirements.txt

# Copy app
COPY app /app/app

RUN chown -R appuser:appuser /app
USER appuser

EXPOSE 8001

CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8001"]
