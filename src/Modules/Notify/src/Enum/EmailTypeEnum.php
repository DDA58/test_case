<?php

declare(strict_types=1);

namespace App\Modules\Notify\Enum;

enum EmailTypeEnum: string
{
    case BeforeThreeDays = 'before_3_days';
    case BeforeOneDay = 'before_1_day';
    case AfterExpireSubscription = 'after_expire_subscription';
    case BeforeSubscriptionExpiration = 'before_subscription_expiration';
}
