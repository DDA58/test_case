<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\RenderEmail;

use App\Modules\Notify\Enum\EmailTypeEnum;

interface RenderEmailServiceInterface
{
    public function handle(EmailTypeEnum $type, array $params = []): string;
}