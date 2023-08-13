<?php

declare(strict_types=1);

namespace App\Email\Service\GetEmailsWithPreExpirationSubscription;

use App\Email\Dto\EmailWithPreExpirationSubscriptionDto;

interface GetEmailsWithPreExpirationSubscriptionServiceInterface
{
    /**
     * @return iterable<EmailWithPreExpirationSubscriptionDto>
     */
    public function handle(int $daysBeforeExpiration): iterable;
}