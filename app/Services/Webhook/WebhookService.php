<?php
namespace App\Services\Webhook;

use App\Models\WebhookEvent;
use App\Services\Auditoria\AuditService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * WebhookService — §90
 * Envía eventos a URLs externas con firma HMAC, idempotencia y reintentos.
 */
class WebhookService
{
    private int $maxIntentos = 5;

    public function __construct(private readonly AuditService $audit) {}

    public function despachar(string $evento, array $payload, string $url, int $orgId, string $secret): WebhookEvent
    {
        $idempotencyKey = hash('sha256', $evento . '|' . $url . '|' . json_encode($payload));

        $existente = WebhookEvent::where('idempotency_key', $idempotencyKey)->where('estado','enviado')->first();
        if ($existente) return $existente;

        return WebhookEvent::create([
            'organizacion_id' => $orgId,
            'evento'          => $evento,
            'url'             => $url,
            'metodo'          => 'POST',
            'payload'         => $payload,
            'estado'          => 'pendiente',
            'idempotency_key' => $idempotencyKey,
            'firma'           => $this->firmar($payload, $secret),
            'request_id'      => (string) Str::uuid(),
        ]);
    }

    public function enviar(WebhookEvent $event, string $secret): void
    {
        if ($event->intentos >= $this->maxIntentos) {
            $event->update(['estado'=>'fallido']);
            return;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-Webhook-Signature' => $event->firma,
                    'X-Webhook-ID'        => $event->uuid,
                    'Content-Type'        => 'application/json',
                ])->post($event->url, $event->payload);

            $event->update([
                'codigo_respuesta'  => $response->status(),
                'respuesta_body'    => substr($response->body(), 0, 5000),
                'headers_respuesta' => $response->headers(),
                'estado'            => $response->successful() ? 'enviado' : 'fallido',
                'intentos'          => $event->intentos + 1,
            ]);

            $this->audit->log(modulo:'webhook',accion:$response->successful()?'send':'failed',
                model:WebhookEvent::class,modelId:$event->id);

        } catch (\Throwable $e) {
            $event->update([
                'estado'          => 'reintentando',
                'intentos'        => $event->intentos + 1,
                'proximo_intento' => now()->addMinutes(min(60, 5 * ($event->intentos + 1))),
                'respuesta_body'  => $e->getMessage(),
            ]);
        }
    }

    private function firmar(array $payload, string $secret): string
    {
        return hash_hmac('sha256', json_encode($payload), $secret);
    }

    public function verificarFirma(string $body, string $firma, string $secret): bool
    {
        return hash_equals(hash_hmac('sha256', $body, $secret), $firma);
    }
}
