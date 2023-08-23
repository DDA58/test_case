<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\StartCommandQueueWorker;

interface StartCommandQueueWorkerServiceInterface
{
    public function handle(int $parentCommandId): void;
}
