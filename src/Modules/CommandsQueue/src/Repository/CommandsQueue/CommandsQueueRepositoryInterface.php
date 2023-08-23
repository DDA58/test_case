<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Repository\CommandsQueue;

use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Entity\CommandsQueueEntity;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

interface CommandsQueueRepositoryInterface
{
    /**
     * @throws CommandsQueueRepositoryException
     */
    public function save(SaveCommandsQueueDto $saveCommandsQueueDto): int;

    /**
     * @param iterable<SaveCommandsQueueDto> $commands
     * @throws CommandsQueueRepositoryException
     */
    public function bulkSave(iterable $commands): bool;

    /**
     * @throws CommandsQueueRepositoryException
     */
    public function concatCommandIdToColumnByParentCommandId(int $parentCommandId): bool;

    /**
     * @throws CommandsQueueRepositoryException
     */
    public function updateStatusByParentCommandId(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status
    ): bool;

    /**
     * @throws CommandsQueueRepositoryException
     */
    public function updateStatusByCommandId(
        int $commandId,
        CommandsExecutionLogStatusEnum $status
    ): bool;

    /**
     * @return iterable<CommandsQueueEntity>
     *     @throws CommandsQueueRepositoryException
     */
    public function getByParentCommandIdAndStatus(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        int $limit,
        bool $forUpdate = false
    ): iterable;

    /**
     * @throws CommandsQueueRepositoryException
     */
    public function updateStatusAndCommandPidByCommandId(
        int $commandId,
        CommandsExecutionLogStatusEnum $status,
        ?int $commandPid,
    ): bool;

    /**
     * @throws CommandsQueueRepositoryException
     */
    public function findByParentCommandIdAndStatus(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        bool $forUpdate = false
    ): ?CommandsQueueEntity;
}
