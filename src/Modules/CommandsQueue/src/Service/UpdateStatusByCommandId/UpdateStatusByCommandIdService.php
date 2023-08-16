<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusByCommandId;

use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

readonly class UpdateStatusByCommandIdService implements
    UpdateStatusByCommandIdServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository,
    ) {
    }

    public function handle(
        int $commandId,
        CommandsExecutionLogStatusEnum $status
    ): bool {
        return $this->commandsQueueRepository->updateStatusByCommandId($commandId, $status);
    }
}