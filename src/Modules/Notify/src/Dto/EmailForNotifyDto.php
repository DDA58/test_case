<?php

declare(strict_types=1);

namespace App\Modules\Notify\Dto;

use App\Modules\Shared\ValueObject\Email;
use App\Modules\Shared\ValueObject\EmailId;
use App\Modules\Shared\ValueObject\UserId;

readonly class EmailForNotifyDto
{
    public function __construct(
        private UserId $userId,
        private string $username,
        private EmailId $emailId,
        private Email $email,
        private bool $emailConfirmed,
        private bool $emailChecked,
        private bool $emailValid,
    ) {
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmailId(): EmailId
    {
        return $this->emailId;
    }

    public function getEmail(): Email
    {
        return $this->email;
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
