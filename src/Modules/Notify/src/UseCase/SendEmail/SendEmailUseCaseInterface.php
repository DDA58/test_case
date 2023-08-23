<?php

declare(strict_types=1);

namespace App\Modules\Notify\UseCase\SendEmail;

use App\Modules\Notify\Enum\EmailTypeEnum;

interface SendEmailUseCaseInterface
{
    /**
     * @param iterable<int> $emailIds
     */
    public function handle(
        int $commandId,
        iterable $emailIds,
        EmailTypeEnum $emailType
    ): void;
}
