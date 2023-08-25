<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusAndCommandPidByCommandId;

use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\CommandsQueue\Service\UpdateStatusAndCommandPidByCommandId\Exception\UpdateStatusAndCommandPidByCommandIdServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;

readonly class UpdateStatusAndCommandPidByCommandIdService implements
    UpdateStatusAndCommandPidByCommandIdServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository,
    ) {
    }

    public function handle(
        CommandId $commandId,
        CommandsExecutionLogStatusEnum $status,
        ?int $commandPid,
    ): bool {
        try {
            return $this->commandsQueueRepository->updateStatusAndCommandPidByCommandId($commandId, $status, $commandPid);
        } catch (CommandsQueueRepositoryException $exception) {
            throw new UpdateStatusAndCommandPidByCommandIdServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
