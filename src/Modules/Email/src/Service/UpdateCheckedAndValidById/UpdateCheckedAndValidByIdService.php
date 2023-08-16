<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\UpdateCheckedAndValidById;

use App\Modules\Email\Repository\Email\EmailRepositoryInterface;
use App\Modules\Shared\ValueObject\EmailId;

readonly class UpdateCheckedAndValidByIdService implements UpdateCheckedAndValidByIdServiceInterface
{
    public function __construct(
        private EmailRepositoryInterface $emailRepository
    ) {
    }

    public function handle(EmailId $id, bool $isChecked, bool $isValid): bool
    {
        return $this->emailRepository->updateCheckedAndValidById($id, $isChecked, $isValid);
    }
}