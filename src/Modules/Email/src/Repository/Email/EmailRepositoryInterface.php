<?php

declare(strict_types=1);

namespace App\Modules\Email\Repository\Email;

use App\Modules\Email\Dto\EmailWithExpiredSubscriptionDto;
use App\Modules\Email\Dto\EmailWithPreExpirationSubscriptionDto;
use App\Modules\Email\Repository\Email\Exception\EmailRepositoryException;
use App\Modules\Shared\ValueObject\EmailId;

interface EmailRepositoryInterface
{
    /**
     * @return iterable<EmailWithExpiredSubscriptionDto>
     * @throws EmailRepositoryException
     */
    public function getEmailsWithExpiredSubscription(): iterable;

    /**
     * @return iterable<EmailWithPreExpirationSubscriptionDto>
     * @throws EmailRepositoryException
     */
    public function getEmailsWithPreExpirationSubscription(int $daysBeforeExpiration): iterable;

    /**
     * @throws EmailRepositoryException
     */
    public function updateCheckedAndValidById(
        EmailId $id,
        bool $isChecked,
        bool $isValid
    ): bool;
}
