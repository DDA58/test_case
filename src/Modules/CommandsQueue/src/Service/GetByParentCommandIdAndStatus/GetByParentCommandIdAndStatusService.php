<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\GetByParentCommandIdAndStatus;

use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

readonly class GetByParentCommandIdAndStatusService implements GetByParentCommandIdAndStatusServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        int $limit,
        bool $forUpdate = false
    ): iterable {
        return $this->commandsQueueRepository->getByParentCommandIdAndStatus(
            $parentCommandId,
            $status,
            $limit,
            $forUpdate
        );
    }
}