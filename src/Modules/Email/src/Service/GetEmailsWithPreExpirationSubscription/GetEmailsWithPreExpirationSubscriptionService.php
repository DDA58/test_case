<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\GetEmailsWithPreExpirationSubscription;

use App\Modules\Email\Repository\Email\EmailRepositoryInterface;
use App\Modules\Email\Repository\Email\Exception\EmailRepositoryException;
use App\Modules\Email\Service\GetEmailsWithPreExpirationSubscription\Exception\GetEmailsWithPreExpirationSubscriptionServiceException;

readonly class GetEmailsWithPreExpirationSubscriptionService implements GetEmailsWithPreExpirationSubscriptionServiceInterface
{
    public function __construct(
        private EmailRepositoryInterface $emailRepository,
    ) {
    }

    public function handle(int $daysBeforeExpiration): iterable
    {
        try {
            yield from $this->emailRepository->getEmailsWithPreExpirationSubscription($daysBeforeExpiration);
        } catch (EmailRepositoryException $exception) {
            throw new GetEmailsWithPreExpirationSubscriptionServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
