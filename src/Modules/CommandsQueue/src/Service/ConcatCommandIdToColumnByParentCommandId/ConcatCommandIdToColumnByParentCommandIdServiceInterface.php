<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId;

use App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId\Exception\ConcatCommandIdToColumnByParentCommandIdServiceException;
use App\Modules\Shared\ValueObject\CommandId;

interface ConcatCommandIdToColumnByParentCommandIdServiceInterface
{
    /**
     * @throws ConcatCommandIdToColumnByParentCommandIdServiceException
     */
    public function handle(CommandId $parentCommandId): bool;
}
