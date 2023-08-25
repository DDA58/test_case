<?php

declare(strict_types=1);

namespace App\Modules\Notify\UseCase\SendEmail;

use App\Modules\Notify\Enum\EmailTypeEnum;
use App\Modules\Shared\ValueObject\CommandId;
use App\Modules\Shared\ValueObject\EmailId;

interface SendEmailUseCaseInterface
{
    public function handle(
        CommandId $commandId,
        EmailId $emailId,
        EmailTypeEnum $emailType
    ): void;
}
