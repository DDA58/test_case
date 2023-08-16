<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId;

interface ConcatCommandIdToColumnByParentCommandIdServiceInterface
{
    public function handle(int $parentCommandId): bool;
}