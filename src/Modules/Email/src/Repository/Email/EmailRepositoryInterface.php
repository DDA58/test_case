<?php

declare(strict_types=1);

namespace App\Modules\Email\Repository\Email;

use App\Modules\Email\Dto\EmailWithExpiredSubscriptionDto;
use App\Modules\Email\Dto\EmailWithPreExpirationSubscriptionDto;
use App\Modules\Shared\ValueObject\EmailId;

interface EmailRepositoryInterface
{
    /**
     * @return iterable<EmailWithExpiredSubscriptionDto>
     */
    public function getEmailsWithExpiredSubscription(): iterable;

    /**
     * @return iterable<EmailWithPreExpirationSubscriptionDto>
     */
    public function getEmailsWithPreExpirationSubscription(int $daysBeforeExpiration): iterable;

    public function updateCheckedAndValidById(
        EmailId $id,
        bool $isChecked,
        bool $isValid
    ): bool;
}