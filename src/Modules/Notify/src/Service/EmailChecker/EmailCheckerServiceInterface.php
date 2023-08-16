<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\EmailChecker;

use App\Modules\Shared\ValueObject\Email;
use App\Modules\Shared\ValueObject\EmailId;

interface EmailCheckerServiceInterface
{
    public function handle(EmailId $id, Email $email): bool;
}