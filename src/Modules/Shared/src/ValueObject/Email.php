<?php

declare(strict_types=1);

namespace App\Modules\Shared\ValueObject;

use App\Modules\Shared\Exception\InvalidArgumentException;

readonly class Email
{
    private const MESSAGE = '[Email] Invalid';

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $email
    ) {
        if (str_contains($this->email, '@') === false) {
            throw new InvalidArgumentException(self::MESSAGE);
        }
    }

    public function getValue(): string
    {
        return $this->email;
    }
}
