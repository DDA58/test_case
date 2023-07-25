<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\EmailSendLogTypeEnum;

class NotifyAboutOneDayBeforeSubscriptionExpirationCommand extends AbstractNotifyBeforeSubscriptionExpirationCommand
{
    private const FROM = 'NotifyAboutOneDayBeforeSubscriptionExpirationCommand@localhost.ru';

    private const NAME = 'notify:one_day_before_subscription_expiration';

    public static function getName(): string
    {
        return self::NAME;
    }

    protected function getEmailSendLogType(): EmailSendLogTypeEnum
    {
        return EmailSendLogTypeEnum::BeforeOneDay;
    }

    protected function getFrom(): string
    {
        return self::FROM;
    }
}
