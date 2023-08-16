<?php

declare(strict_types=1);

namespace App\Modules\Notify\Event;

use App\Modules\Notify\Enum\EmailTypeEnum;

readonly class EmailSentSuccessfulEvent
{
    public const NAME = 'notify.email_sent';

    public function __construct(
        private EmailTypeEnum $type,
        private int $commandId,
        private int $emailId,
        private bool $emailConfirmed,
        private bool $emailChecked,
        private bool $emailValid,
    ) {
    }

    public function getType(): EmailTypeEnum
    {
        return $this->type;
    }

    public function getCommandId(): int
    {
        return $this->commandId;
    }

    public function getEmailId(): int
    {
        return $this->emailId;
    }

    public function isEmailConfirmed(): bool
    {
        return $this->emailConfirmed;
    }

    public function isEmailChecked(): bool
    {
        return $this->emailChecked;
    }

    public function isEmailValid(): bool
    {
        return $this->emailValid;
    }
}