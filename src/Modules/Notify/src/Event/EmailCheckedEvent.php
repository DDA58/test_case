<?php

declare(strict_types=1);

namespace App\Modules\Notify\Event;

use App\Modules\Shared\ValueObject\EmailId;

readonly class EmailCheckedEvent
{
    public const NAME = 'notify.email_checked';

    public function __construct(
        private EmailId $emailId,
        private bool $isValid
    ) {
    }

    public function getEmailId(): EmailId
    {
        return $this->emailId;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }
}