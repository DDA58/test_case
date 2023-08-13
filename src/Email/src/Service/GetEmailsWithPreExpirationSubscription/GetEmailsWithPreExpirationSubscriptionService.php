<?php

declare(strict_types=1);

namespace App\Email\Service\GetEmailsWithPreExpirationSubscription;

use App\Email\Repository\Email\EmailRepositoryInterface;

readonly class GetEmailsWithPreExpirationSubscriptionService implements GetEmailsWithPreExpirationSubscriptionServiceInterface
{
    public function __construct(
        private EmailRepositoryInterface $emailRepository,
    ) {
    }

    public function handle(int $daysBeforeExpiration): iterable
    {
        yield from $this->emailRepository->getEmailsWithPreExpirationSubscription($daysBeforeExpiration);
    }
}