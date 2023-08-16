<?php

declare(strict_types=1);

namespace App\Modules\Notify\Dto;

use App\Modules\Notify\Enum\EmailTypeEnum;

readonly class SaveEmailsSendLogDto
{
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