<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\EmailSendLogTypeEnum;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'notify:one_day_before_subscription_expiration')]
class NotifyAboutOneDayBeforeSubscriptionExpirationCommand extends AbstractNotifyByEmailIdsCommand
{
    private const FROM = 'NotifyAboutOneDayBeforeSubscriptionExpirationCommand@localhost.ru';

    protected function getEmailSendLogType(): EmailSendLogTypeEnum
    {
        return EmailSendLogTypeEnum::BeforeOneDay;
    }

    protected function getFrom(): string
    {
        return self::FROM;
    }
}
