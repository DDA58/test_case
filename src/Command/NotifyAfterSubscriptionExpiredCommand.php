<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\EmailSendLogTypeEnum;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'notify:after_subscription_expired')]
class NotifyAfterSubscriptionExpiredCommand extends AbstractNotifyByEmailIdsCommand
{
    private const FROM = 'NotifyAfterSubscriptionExpiredCommand@localhost.ru';

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
