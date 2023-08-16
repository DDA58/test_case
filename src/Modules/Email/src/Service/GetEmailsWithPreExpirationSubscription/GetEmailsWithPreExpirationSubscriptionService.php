<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\GetEmailsWithPreExpirationSubscription;

use App\Modules\Email\Repository\Email\EmailRepositoryInterface;

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