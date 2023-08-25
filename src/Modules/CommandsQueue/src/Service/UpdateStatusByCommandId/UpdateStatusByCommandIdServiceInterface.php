<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusByCommandId;

use App\Modules\CommandsQueue\Service\UpdateStatusByCommandId\Exception\UpdateStatusByCommandIdServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;

interface UpdateStatusByCommandIdServiceInterface
{
    /**
     * @throws UpdateStatusByCommandIdServiceException
     */
    public function handle(
        CommandId $commandId,
        CommandsExecutionLogStatusEnum $status
    ): bool;
}
