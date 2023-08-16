<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\GetEmailsWithExpiredSubscription;

use App\Modules\Email\Dto\EmailWithExpiredSubscriptionDto;

interface GetEmailsWithExpiredSubscriptionServiceInterface
{
    /**
     * @return iterable<EmailWithExpiredSubscriptionDto>
     */
    public function handle(): iterable;
}