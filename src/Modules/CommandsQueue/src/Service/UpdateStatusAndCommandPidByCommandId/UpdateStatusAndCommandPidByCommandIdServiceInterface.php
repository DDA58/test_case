<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusAndCommandPidByCommandId;

use App\Modules\CommandsQueue\Service\UpdateStatusAndCommandPidByCommandId\Exception\UpdateStatusAndCommandPidByCommandIdServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;

interface UpdateStatusAndCommandPidByCommandIdServiceInterface
{
    /**
     * @throws UpdateStatusAndCommandPidByCommandIdServiceException
     */
    public function handle(
        CommandId $commandId,
        CommandsExecutionLogStatusEnum $status,
        ?int $commandPid,
    ): bool;
}
