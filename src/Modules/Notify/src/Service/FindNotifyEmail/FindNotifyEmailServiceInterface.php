<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\FindNotifyEmail;

use App\Modules\Notify\Dto\EmailForNotifyDto;
use App\Modules\Shared\ValueObject\EmailId;

interface FindNotifyEmailServiceInterface
{
    public function findByEmailId(
        EmailId $emailId,
        bool $forUpdate = false
    ): ?EmailForNotifyDto;
}