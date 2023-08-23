<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\IterableEmailIdsToBulkSaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto;
use App\Modules\CommandsQueue\Service\IterableEmailIdsToBulkSaveCommandsQueue\Exception\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterException;

interface IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterInterface
{
    /**
     * @throws IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterException
     */
    public function handle(IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto $dto): void;
}
