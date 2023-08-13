<?php

declare(strict_types=1);

namespace App\Email\Service\GetEmailsWithExpiredSubscription;

use App\Email\Dto\EmailWithExpiredSubscriptionDto;

interface GetEmailsWithExpiredSubscriptionServiceInterface
{
    /**
     * @return iterable<EmailWithExpiredSubscriptionDto>
     */
    public function handle(): iterable;
}