<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId;

use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;

readonly class ConcatCommandIdToColumnByParentCommandIdService implements
    ConcatCommandIdToColumnByParentCommandIdServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository,
    ) {
    }

    public function handle(int $parentCommandId): bool
    {
        return $this->commandsQueueRepository->concatCommandIdToColumnByParentCommandId($parentCommandId);
    }
}