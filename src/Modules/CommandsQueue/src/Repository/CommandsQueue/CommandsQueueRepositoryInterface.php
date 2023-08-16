<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Repository\CommandsQueue;

use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Entity\CommandsQueueEntity;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

interface CommandsQueueRepositoryInterface
{
    public function save(SaveCommandsQueueDto $saveCommandsQueueDto): int;

    /**
     * @param iterable<SaveCommandsQueueDto> $commands
     */
    public function bulkSave(iterable $commands): bool;

    public function concatCommandIdToColumnByParentCommandId(int $parentCommandId): bool;

    public function updateStatusByParentCommandId(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status
    ): bool;

    public function updateStatusByCommandId(
        int $commandId,
        CommandsExecutionLogStatusEnum $status
    ): bool;

    /**
     * @return iterable<CommandsQueueEntity>
     */
    public function getByParentCommandIdAndStatus(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        int $limit,
        bool $forUpdate = false
    ): iterable;

    public function updateStatusAndCommandPidByCommandId(
        int $commandId,
        CommandsExecutionLogStatusEnum $status,
        ?int $commandPid,
    ): bool;

    public function findByParentCommandIdAndStatus(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        bool $forUpdate = false
    ): ?CommandsQueueEntity;
}