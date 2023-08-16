<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusAndCommandPidByCommandId;

use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

readonly class UpdateStatusAndCommandPidByCommandIdService implements
    UpdateStatusAndCommandPidByCommandIdServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository,
    ) {
    }

    public function handle(
        int $commandId,
        CommandsExecutionLogStatusEnum $status,
        ?int $commandPid,
    ): bool {
        return $this->commandsQueueRepository->updateStatusAndCommandPidByCommandId($commandId, $status, $commandPid);
    }
}