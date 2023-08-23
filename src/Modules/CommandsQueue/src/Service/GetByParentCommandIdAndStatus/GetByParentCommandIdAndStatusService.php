<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\GetByParentCommandIdAndStatus;

use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\CommandsQueue\Service\GetByParentCommandIdAndStatus\Exception\GetByParentCommandIdAndStatusServiceException;
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
        try {
            return $this->commandsQueueRepository->getByParentCommandIdAndStatus(
                $parentCommandId,
                $status,
                $limit,
                $forUpdate
            );
        } catch (CommandsQueueRepositoryException $exception) {
            throw new GetByParentCommandIdAndStatusServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
