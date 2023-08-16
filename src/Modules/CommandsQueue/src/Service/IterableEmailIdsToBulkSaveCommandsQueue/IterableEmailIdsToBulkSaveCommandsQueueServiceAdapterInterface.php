<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\IterableEmailIdsToBulkSaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto;

interface IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterInterface
{
    public function handle(IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto $dto): void;
}