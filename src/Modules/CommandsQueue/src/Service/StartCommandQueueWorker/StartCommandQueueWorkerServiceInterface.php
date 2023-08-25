<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\StartCommandQueueWorker;

use App\Modules\Shared\ValueObject\CommandId;

interface StartCommandQueueWorkerServiceInterface
{
    public function handle(CommandId $parentCommandId): void;
}
