<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\EmailSendLogTypeEnum;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'notify:three_days_before_subscription_expiration')]
class NotifyAboutThreeDaysBeforeSubscriptionExpirationCommand extends AbstractNotifyByEmailIdsCommand
{
    private const FROM = 'NotifyAboutThreeDaysBeforeSubscriptionExpirationCommand@localhost.ru';

    protected function getEmailSendLogType(): EmailSendLogTypeEnum
    {
        return EmailSendLogTypeEnum::BeforeThreeDays;
    }

    protected function getFrom(): string
    {
        return self::FROM;
    }
}
