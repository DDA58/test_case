<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusByCommandId;

use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\CommandsQueue\Service\UpdateStatusByCommandId\Exception\UpdateStatusByCommandIdServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;

readonly class UpdateStatusByCommandIdService implements
    UpdateStatusByCommandIdServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository,
    ) {
    }

    public function handle(
        CommandId $commandId,
        CommandsExecutionLogStatusEnum $status
    ): bool {
        try {
            return $this->commandsQueueRepository->updateStatusByCommandId($commandId, $status);
        } catch (CommandsQueueRepositoryException $exception) {
            throw new UpdateStatusByCommandIdServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
