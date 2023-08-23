<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId;

use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId\Exception\ConcatCommandIdToColumnByParentCommandIdServiceException;

readonly class ConcatCommandIdToColumnByParentCommandIdService implements
    ConcatCommandIdToColumnByParentCommandIdServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository,
    ) {
    }

    public function handle(int $parentCommandId): bool
    {
        try {
            return $this->commandsQueueRepository->concatCommandIdToColumnByParentCommandId($parentCommandId);
        } catch (CommandsQueueRepositoryException $exception) {
            throw new ConcatCommandIdToColumnByParentCommandIdServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
