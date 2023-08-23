<?php

declare(strict_types=1);

namespace App\Modules\Notify\Repository\EmailForNotify;

use App\Modules\Notify\Dto\EmailForNotifyDto;
use App\Modules\Notify\Repository\EmailForNotify\Exception\EmailForNotifyRepositoryException;
use App\Modules\Shared\ValueObject\EmailId;

interface EmailForNotifyRepositoryInterface
{
    /**
     * @throws EmailForNotifyRepositoryException
     */
    public function findByEmailId(
        EmailId $emailId,
        bool $forUpdate = false
    ): ?EmailForNotifyDto;
}
