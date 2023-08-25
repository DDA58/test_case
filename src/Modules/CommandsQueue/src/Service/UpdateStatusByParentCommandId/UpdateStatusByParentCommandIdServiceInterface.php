<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusByParentCommandId;

use App\Modules\CommandsQueue\Service\UpdateStatusByParentCommandId\Exception\UpdateStatusByParentCommandIdServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;

interface UpdateStatusByParentCommandIdServiceInterface
{
    /**
     * @throws UpdateStatusByParentCommandIdServiceException
     */
    public function handle(
        CommandId $parentCommandId,
        CommandsExecutionLogStatusEnum $status
    ): bool;
}
