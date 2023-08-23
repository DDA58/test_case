<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\GetEmailsWithExpiredSubscription;

use App\Modules\Email\Dto\EmailWithExpiredSubscriptionDto;
use App\Modules\Email\Service\GetEmailsWithExpiredSubscription\Exception\GetEmailsWithExpiredSubscriptionServiceException;

interface GetEmailsWithExpiredSubscriptionServiceInterface
{
    /**
     * @return iterable<EmailWithExpiredSubscriptionDto>
     * @throws GetEmailsWithExpiredSubscriptionServiceException
     */
    public function handle(): iterable;
}
