<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\UpdateStatusByParentCommandId;

use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

interface UpdateStatusByParentCommandIdServiceInterface
{
    public function handle(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status
    ): bool;
}