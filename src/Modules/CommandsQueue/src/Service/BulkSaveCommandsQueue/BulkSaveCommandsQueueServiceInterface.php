<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\BulkSaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Service\BulkSaveCommandsQueue\Exception\BulkSaveCommandsQueueServiceException;

interface BulkSaveCommandsQueueServiceInterface
{
    /**
     * @param iterable<int, SaveCommandsQueueDto> $commands
     * @throws BulkSaveCommandsQueueServiceException
     */
    public function handle(iterable $commands): void;
}
