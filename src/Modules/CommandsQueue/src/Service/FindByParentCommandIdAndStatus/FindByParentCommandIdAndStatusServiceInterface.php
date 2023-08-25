<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\FindByParentCommandIdAndStatus;

use App\Modules\CommandsQueue\Entity\CommandsQueueEntity;
use App\Modules\CommandsQueue\Service\FindByParentCommandIdAndStatus\Exception\FindByParentCommandIdAndStatusServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;

interface FindByParentCommandIdAndStatusServiceInterface
{
    /**
     * @throws FindByParentCommandIdAndStatusServiceException
     */
    public function handle(
        CommandId $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        bool $forUpdate = false
    ): ?CommandsQueueEntity;
}
