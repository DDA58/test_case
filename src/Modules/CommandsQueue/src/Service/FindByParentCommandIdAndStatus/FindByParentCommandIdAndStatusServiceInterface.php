<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\FindByParentCommandIdAndStatus;

use App\Modules\CommandsQueue\Entity\CommandsQueueEntity;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

interface FindByParentCommandIdAndStatusServiceInterface
{
    public function handle(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        bool $forUpdate = false
    ): ?CommandsQueueEntity;
}