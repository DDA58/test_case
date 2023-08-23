<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\BeforeSubscriptionExpirationEmailTypeDetector;

use App\Modules\Notify\Enum\EmailTypeEnum;

interface BeforeSubscriptionExpirationEmailTypeDetectorServiceInterface
{
    public function handle(int $daysBeforeExpiration): EmailTypeEnum;
}
