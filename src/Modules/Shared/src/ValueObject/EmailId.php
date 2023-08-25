<?php

declare(strict_types=1);

namespace App\Modules\Shared\ValueObject;

use App\Modules\Shared\Exception\InvalidArgumentException;

readonly class EmailId
{
    private const MESSAGE = '[EmailId] Invalid';

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        private int $emailId
    ) {
        if ($this->emailId <= 0) {
            throw new InvalidArgumentException(self::MESSAGE);
        }
    }

    public function getValue(): int
    {
        return $this->emailId;
    }
}
