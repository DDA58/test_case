<?php

declare(strict_types=1);

namespace App\Modules\Shared\ValueObject;

readonly class EmailId
{
    public function __construct(
        private int $emailId
    ) {
    }

    public function getValue(): int
    {
        return $this->emailId;
    }
}
