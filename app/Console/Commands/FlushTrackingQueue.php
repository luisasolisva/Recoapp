<?php

namespace App\Console\Commands;

use App\Services\TrackingQueueService;
use Illuminate\Console\Command;

/**
 * Comando CLI para reenviar posiciones pendientes.
 */
class FlushTrackingQueue extends Command
{
    protected $signature = 'tracking:flush';

    protected $description = 'Reintenta enviar posiciones de recorrido que fallaron previamente';

    public function __construct(private readonly TrackingQueueService $queueService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $results = $this->queueService->flush();
        foreach ($results as $result) {
            $this->line(json_encode($result));
        }
        return self::SUCCESS;
    }
}
