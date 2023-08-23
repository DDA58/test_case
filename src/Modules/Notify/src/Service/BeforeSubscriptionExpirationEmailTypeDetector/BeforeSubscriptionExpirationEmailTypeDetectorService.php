<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\BeforeSubscriptionExpirationEmailTypeDetector;

use App\Modules\Notify\Enum\EmailTypeEnum;

readonly class BeforeSubscriptionExpirationEmailTypeDetectorService implements
    BeforeSubscriptionExpirationEmailTypeDetectorServiceInterface
{
    public function handle(int $daysBeforeExpiration): EmailTypeEnum
    {
        return match ($daysBeforeExpiration) {
            1 => EmailTypeEnum::BeforeOneDay,
            3 => EmailTypeEnum::BeforeThreeDays,
            default => EmailTypeEnum::BeforeSubscriptionExpiration,
        };
    }
}
