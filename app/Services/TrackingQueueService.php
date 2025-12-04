<?php

namespace App\Services;

use App\Models\QueuedPosition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Responsable de almacenar posiciones fallidas y reintentarlas.
 * Usa una tabla local simple para guardar el JSON del payload.
 */
class TrackingQueueService
{
    public function __construct(private readonly ExternalApiService $externalApi)
    {
    }

    public function enqueuePosition(string $recorridoId, array $payload): void
    {
        QueuedPosition::create([
            'recorrido_id' => $recorridoId,
            'payload' => $payload
        ]);
    }

    public function pending(): array
    {
        return QueuedPosition::query()
            ->orderBy('created_at')
            ->get()
            ->map(fn (QueuedPosition $position) => $position->toArray())
            ->all();
    }

    public function flush(): array
    {
        $results = [];

        DB::transaction(function () use (&$results) {
            /** @var QueuedPosition $position */
            foreach (QueuedPosition::lockForUpdate()->cursor() as $position) {
                try {
                    $this->externalApi->storePosition($position->recorrido_id, $position->payload);
                    $results[] = [
                        'id' => $position->id,
                        'status' => 'sent'
                    ];
                    $position->delete();
                } catch (\Throwable $exception) {
                    Log::error('No se pudo reintentar posición', [
                        'queued' => $position->id,
                        'error' => $exception->getMessage()
                    ]);
                    $results[] = [
                        'id' => $position->id,
                        'status' => 'failed',
                        'error' => $exception->getMessage()
                    ];
                }
            }
        });

        return $results;
    }
}
