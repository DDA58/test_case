<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\SaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;

readonly class SaveCommandsQueueService implements SaveCommandsQueueServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository
    ) {
    }

    public function handle(SaveCommandsQueueDto $saveCommandsQueueDto): int
    {
        return $this->commandsQueueRepository->save($saveCommandsQueueDto);
    }
}