<?php

namespace Rhaima\VoltPanel\Webhooks;

use Illuminate\Support\Facades\Http;
use Rhaima\VoltPanel\Models\Webhook;

class WebhookManager
{
    public function dispatch(string $event, array $payload): void
    {
        $webhooks = Webhook::where('event', $event)
            ->where('is_active', true)
            ->get();

        foreach ($webhooks as $webhook) {
            $this->send($webhook, $payload);
        }
    }

    protected function send(Webhook $webhook, array $payload): void
    {
        try {
            $response = Http::timeout($webhook->timeout ?? 10)
                ->withHeaders($webhook->headers ?? [])
                ->post($webhook->url, [
                    'event' => $webhook->event,
                    'payload' => $payload,
                    'timestamp' => now()->toIso8601String(),
                ]);

            $webhook->increment('successful_calls');
            $webhook->update(['last_called_at' => now()]);
        } catch (\Exception $e) {
            $webhook->increment('failed_calls');
            $webhook->update([
                'last_error' => $e->getMessage(),
                'last_called_at' => now(),
            ]);
        }
    }

    public function register(string $event, string $url, ?array $headers = []): Webhook
    {
        return Webhook::create([
            'event' => $event,
            'url' => $url,
            'headers' => $headers,
            'is_active' => true,
        ]);
    }
}
