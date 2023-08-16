<?php

declare(strict_types=1);

namespace App\Modules\Notify\UseCase\SendEmail;

use App\Modules\Notify\Enum\EmailTypeEnum;

interface SendEmailUseCaseInterface
{
    public function handle(
        int $commandId,
        iterable $emailIds,
        EmailTypeEnum $emailType
    ): void;
}