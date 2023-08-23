<?php

declare(strict_types=1);

namespace App\Modules\Notify\Dto;

readonly class EmailForNotifyDto
{
    public function __construct(
        private string $userUuid,
        private string $username,
        private int $emailId,
        private string $email,
        private bool $emailConfirmed,
        private bool $emailChecked,
        private bool $emailValid,
    ) {
    }

    public function getUserUuid(): string
    {
        return $this->userUuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmailId(): int
    {
        return $this->emailId;
    }

    public function getEmail(): string
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
