<?php

declare(strict_types=1);

namespace App\Modules\Shared\ValueObject;

use InvalidArgumentException;

readonly class Email
{
    public function __construct(
        private string $email
    ) {
        if (str_contains($this->email, '@') === false) {
            throw new InvalidArgumentException('Email invalid');
        }
    }

    public function getValue(): string
    {
        return $this->email;
    }
}
