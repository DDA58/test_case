<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\FindNotifyEmail;

use App\Modules\Notify\Dto\EmailForNotifyDto;
use App\Modules\Notify\Repository\EmailForNotify\EmailForNotifyRepositoryInterface;
use App\Modules\Shared\ValueObject\EmailId;

readonly class FindNotifyEmailService implements FindNotifyEmailServiceInterface
{
    public function __construct(
        private EmailForNotifyRepositoryInterface $emailForNotifyRepository
    ) {
    }

    public function findByEmailId(EmailId $emailId, bool $forUpdate = false): ?EmailForNotifyDto
    {
        return $this->emailForNotifyRepository->findByEmailId($emailId, $forUpdate);
    }
}