<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusByCommandId;

use App\Modules\CommandsQueue\Service\UpdateStatusByCommandId\Exception\UpdateStatusByCommandIdServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

interface UpdateStatusByCommandIdServiceInterface
{
    /**
     * @throws UpdateStatusByCommandIdServiceException
     */
    public function handle(
        int $commandId,
        CommandsExecutionLogStatusEnum $status
    ): bool;
}
