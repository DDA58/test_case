<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\SendEmail;

use App\Modules\Notify\Dto\EmailForNotifyDto;
use App\Modules\Notify\Enum\EmailTypeEnum;

interface SendEmailServiceInterface
{
    public function handle(
        EmailTypeEnum $type,
        EmailForNotifyDto $email,
        string $text,
        int $commandId,
    ): void;
}