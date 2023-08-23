<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\GetByParentCommandIdAndStatus;

use App\Modules\CommandsQueue\Entity\CommandsQueueEntity;
use App\Modules\CommandsQueue\Service\GetByParentCommandIdAndStatus\Exception\GetByParentCommandIdAndStatusServiceException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

interface GetByParentCommandIdAndStatusServiceInterface
{
    /**
     * @return iterable<CommandsQueueEntity>
     * @throws GetByParentCommandIdAndStatusServiceException
     */
    public function handle(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        int $limit,
        bool $forUpdate = false
    ): iterable;
}
