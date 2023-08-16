<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\GetEmailsWithExpiredSubscription;

use App\Modules\Email\Repository\Email\EmailRepositoryInterface;

readonly class GetEmailsWithExpiredSubscriptionService implements GetEmailsWithExpiredSubscriptionServiceInterface
{
    public function __construct(
        private EmailRepositoryInterface $emailRepository,
    ) {
    }

    public function handle(): iterable
    {
        yield from $this->emailRepository->getEmailsWithExpiredSubscription();
    }
}