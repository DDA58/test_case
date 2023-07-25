<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\EmailSendLogTypeEnum;

class NotifyAboutThreeDaysBeforeSubscriptionExpirationCommand extends AbstractNotifyBeforeSubscriptionExpirationCommand
{
    private const FROM = 'NotifyAboutThreeDaysBeforeSubscriptionExpirationCommand@localhost.ru';

    private const NAME = 'notify:three_days_before_subscription_expiration';

    public static function getName(): string
    {
        return self::NAME;
    }

    protected function getEmailSendLogType(): EmailSendLogTypeEnum
    {
        return EmailSendLogTypeEnum::BeforeThreeDays;
    }

    protected function getFrom(): string
    {
        return self::FROM;
    }
}
