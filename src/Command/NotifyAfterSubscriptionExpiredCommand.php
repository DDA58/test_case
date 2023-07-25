<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\EmailSendLogTypeEnum;

class NotifyAfterSubscriptionExpiredCommand extends AbstractNotifyBeforeSubscriptionExpirationCommand
{
    private const FROM = 'NotifyAfterSubscriptionExpiredCommand@localhost.ru';

    private const NAME = 'notify:after_subscription_expired';

    public static function getName(): string
    {
        return self::NAME;
    }

    protected function getEmailSendLogType(): EmailSendLogTypeEnum
    {
        return EmailSendLogTypeEnum::AfterExpireSubscription;
    }

    protected function getFrom(): string
    {
        return self::FROM;
    }

    protected function getEmailTemplate(): string
    {
        return '%s, your subscription was expired';
    }
}
