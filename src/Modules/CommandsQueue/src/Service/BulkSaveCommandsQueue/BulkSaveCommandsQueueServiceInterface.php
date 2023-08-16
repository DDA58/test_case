<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\BulkSaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;

interface BulkSaveCommandsQueueServiceInterface
{
    /**
     * @param iterable<SaveCommandsQueueDto> $commands
     */
    public function handle(iterable $commands): void;
}