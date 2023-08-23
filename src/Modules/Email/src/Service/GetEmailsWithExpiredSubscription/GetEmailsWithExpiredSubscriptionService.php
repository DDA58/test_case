<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\GetEmailsWithExpiredSubscription;

use App\Modules\Email\Repository\Email\EmailRepositoryInterface;
use App\Modules\Email\Repository\Email\Exception\EmailRepositoryException;
use App\Modules\Email\Service\GetEmailsWithExpiredSubscription\Exception\GetEmailsWithExpiredSubscriptionServiceException;

readonly class GetEmailsWithExpiredSubscriptionService implements GetEmailsWithExpiredSubscriptionServiceInterface
{
    public function __construct(
        private EmailRepositoryInterface $emailRepository,
    ) {
    }

    public function handle(): iterable
    {
        try {
            yield from $this->emailRepository->getEmailsWithExpiredSubscription();
        } catch (EmailRepositoryException $exception) {
            throw new GetEmailsWithExpiredSubscriptionServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
