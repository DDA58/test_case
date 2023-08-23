<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\SaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\CommandsQueue\Service\SaveCommandsQueue\Exception\SaveCommandsQueueServiceException;

readonly class SaveCommandsQueueService implements SaveCommandsQueueServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository
    ) {
    }

    public function handle(SaveCommandsQueueDto $saveCommandsQueueDto): int
    {
        try {
            return $this->commandsQueueRepository->save($saveCommandsQueueDto);
        } catch (CommandsQueueRepositoryException $exception) {
            throw new SaveCommandsQueueServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
