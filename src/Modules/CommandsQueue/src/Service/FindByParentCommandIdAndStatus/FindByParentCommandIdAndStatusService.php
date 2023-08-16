<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\FindByParentCommandIdAndStatus;

use App\Modules\CommandsQueue\Entity\CommandsQueueEntity;
use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

readonly class FindByParentCommandIdAndStatusService implements FindByParentCommandIdAndStatusServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository
    ) {
    }

    public function handle(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        bool $forUpdate = false
    ): ?CommandsQueueEntity {
        return $this->commandsQueueRepository->findByParentCommandIdAndStatus(
            $parentCommandId,
            $status,
            $forUpdate
        );
    }
}