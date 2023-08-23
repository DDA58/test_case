<?php

declare(strict_types=1);

namespace App\Modules\Email\Dto;

readonly class EmailWithPreExpirationSubscriptionDto
{
    public function __construct(
        private int $emailId
    ) {
    }

    public function getEmailId(): int
    {
        return $this->emailId;
    }
}
