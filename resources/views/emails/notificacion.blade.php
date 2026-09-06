<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notificacion->titulo ?? 'Notificación' }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1f5f9; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .header { background: #2563eb; color: white; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 20px; font-weight: 600; }
        .body { padding: 32px; color: #0f172a; line-height: 1.6; }
        .body p { margin: 0 0 16px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .badge-info { background: #e0f2fe; color: #075985; }
        .badge-exito { background: #dcfce7; color: #166534; }
        .badge-advertencia { background: #fef3c7; color: #92400e; }
        .badge-peligro { background: #fee2e2; color: #991b1b; }
        .footer { padding: 16px 32px; background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
    </div>
    <div class="body">
        <span class="badge badge-{{ $notificacion->tipo ?? 'info' }}">
            {{ ucfirst($notificacion->tipo ?? 'info') }}
        </span>
        <h2 style="margin: 16px 0 8px; font-size: 18px;">{{ $notificacion->titulo }}</h2>
        <p>{{ $notificacion->cuerpo }}</p>
        @if(isset($accionUrl) && isset($accionLabel))
        <div style="margin-top: 24px;">
            <a href="{{ $accionUrl }}" style="background: #2563eb; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-block;">
                {{ $accionLabel }}
            </a>
        </div>
        @endif
    </div>
    <div class="footer">
        Este mensaje fue enviado desde {{ config('app.name') }} · {{ now()->format('d/m/Y H:i') }}
    </div>
</div>
</body>
</html>
