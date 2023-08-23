<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId;

use App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId\Exception\ConcatCommandIdToColumnByParentCommandIdServiceException;

interface ConcatCommandIdToColumnByParentCommandIdServiceInterface
{
    /**
     * @throws ConcatCommandIdToColumnByParentCommandIdServiceException
     */
    public function handle(int $parentCommandId): bool;
}
