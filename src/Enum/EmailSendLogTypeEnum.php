<?php

declare(strict_types=1);

namespace App\Enum;

enum EmailSendLogTypeEnum: string
{
    case BeforeThreeDays = 'before_3_days';
    case BeforeOneDay = 'before_1_day';
    case AfterExpireSubscription = 'after_expire_subscription';
}
