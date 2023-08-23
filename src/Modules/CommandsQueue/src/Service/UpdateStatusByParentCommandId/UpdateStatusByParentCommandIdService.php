<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusByParentCommandId;

use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\CommandsQueue\Service\UpdateStatusByParentCommandId\Exception\UpdateStatusByParentCommandIdServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

readonly class UpdateStatusByParentCommandIdService implements
    UpdateStatusByParentCommandIdServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository,
    ) {
    }

    public function handle(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status
    ): bool {
        try {
            return $this->commandsQueueRepository->updateStatusByParentCommandId($parentCommandId, $status);
        } catch (CommandsQueueRepositoryException $exception) {
            throw new UpdateStatusByParentCommandIdServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
