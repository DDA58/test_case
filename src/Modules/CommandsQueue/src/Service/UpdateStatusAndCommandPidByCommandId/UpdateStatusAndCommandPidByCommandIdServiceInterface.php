<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusAndCommandPidByCommandId;

use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

interface UpdateStatusAndCommandPidByCommandIdServiceInterface
{
    public function handle(
        int $commandId,
        CommandsExecutionLogStatusEnum $status,
        ?int $commandPid,
    ): bool;
}