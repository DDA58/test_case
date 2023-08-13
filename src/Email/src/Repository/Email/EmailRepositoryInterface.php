<?php

declare(strict_types=1);

namespace App\Email\Repository\Email;

use App\Email\Dto\EmailWithExpiredSubscriptionDto;
use App\Email\Dto\EmailWithPreExpirationSubscriptionDto;

interface EmailRepositoryInterface
{
    /**
     * @return iterable<EmailWithExpiredSubscriptionDto>
     */
    public function getEmailsWithExpiredSubscription(): iterable;

    /**
     * @return iterable<EmailWithPreExpirationSubscriptionDto>
     */
    public function getEmailsWithPreExpirationSubscription(int $daysBeforeExpiration): iterable;
}