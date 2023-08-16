<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\RenderEmail;

use App\Modules\Notify\Enum\EmailTypeEnum;

readonly class RenderEmailService implements RenderEmailServiceInterface
{
    public function handle(
        EmailTypeEnum $type,
        array $params = []
    ): string {
        $template = match($type) {
            EmailTypeEnum::BeforeOneDay => '%s, your subscription is expiring in one day',
            EmailTypeEnum::BeforeThreeDays => '%s, your subscription is expiring in three day',
            EmailTypeEnum::BeforeSubscriptionExpiration => '%s, your subscription is expiring soon',
            EmailTypeEnum::AfterExpireSubscription => '%s, your subscription was expired',
        };

        return sprintf($template, ...$params);
    }
}