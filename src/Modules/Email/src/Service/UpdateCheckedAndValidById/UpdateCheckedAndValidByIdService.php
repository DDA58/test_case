<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\UpdateCheckedAndValidById;

use App\Modules\Email\Repository\Email\EmailRepositoryInterface;
use App\Modules\Email\Repository\Email\Exception\EmailRepositoryException;
use App\Modules\Email\Service\UpdateCheckedAndValidById\Exception\UpdateCheckedAndValidByIdServiceException;
use App\Modules\Shared\ValueObject\EmailId;

readonly class UpdateCheckedAndValidByIdService implements UpdateCheckedAndValidByIdServiceInterface
{
    public function __construct(
        private EmailRepositoryInterface $emailRepository
    ) {
    }

    public function handle(EmailId $id, bool $isChecked, bool $isValid): bool
    {
        try {
            return $this->emailRepository->updateCheckedAndValidById($id, $isChecked, $isValid);
        } catch (EmailRepositoryException $exception) {
            throw new UpdateCheckedAndValidByIdServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
