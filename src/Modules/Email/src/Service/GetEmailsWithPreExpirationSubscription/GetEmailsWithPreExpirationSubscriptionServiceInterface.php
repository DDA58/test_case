<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\GetEmailsWithPreExpirationSubscription;

use App\Modules\Email\Dto\EmailWithPreExpirationSubscriptionDto;
use App\Modules\Email\Service\GetEmailsWithPreExpirationSubscription\Exception\GetEmailsWithPreExpirationSubscriptionServiceException;

interface GetEmailsWithPreExpirationSubscriptionServiceInterface
{
    /**
     * @return iterable<EmailWithPreExpirationSubscriptionDto>
     * @throws GetEmailsWithPreExpirationSubscriptionServiceException
     */
    public function handle(int $daysBeforeExpiration): iterable;
}
