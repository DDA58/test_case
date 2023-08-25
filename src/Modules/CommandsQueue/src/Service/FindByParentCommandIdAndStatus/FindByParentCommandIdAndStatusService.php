<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\FindByParentCommandIdAndStatus;

use App\Modules\CommandsQueue\Entity\CommandsQueueEntity;
use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\CommandsQueue\Service\FindByParentCommandIdAndStatus\Exception\FindByParentCommandIdAndStatusServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;

readonly class FindByParentCommandIdAndStatusService implements FindByParentCommandIdAndStatusServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository
    ) {
    }

    public function handle(
        CommandId $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        bool $forUpdate = false
    ): ?CommandsQueueEntity {
        try {
            return $this->commandsQueueRepository->findByParentCommandIdAndStatus(
                $parentCommandId,
                $status,
                $forUpdate
            );
        } catch (CommandsQueueRepositoryException $exception) {
            throw new FindByParentCommandIdAndStatusServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
