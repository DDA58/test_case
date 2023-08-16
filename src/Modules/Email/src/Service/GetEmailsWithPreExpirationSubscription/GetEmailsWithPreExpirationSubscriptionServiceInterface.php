<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\GetEmailsWithPreExpirationSubscription;

use App\Modules\Email\Dto\EmailWithPreExpirationSubscriptionDto;

interface GetEmailsWithPreExpirationSubscriptionServiceInterface
{
    /**
     * @return iterable<EmailWithPreExpirationSubscriptionDto>
     */
    public function handle(int $daysBeforeExpiration): iterable;
}