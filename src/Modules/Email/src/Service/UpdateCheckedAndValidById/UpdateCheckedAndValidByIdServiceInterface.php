<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\UpdateCheckedAndValidById;

use App\Modules\Email\Service\UpdateCheckedAndValidById\Exception\UpdateCheckedAndValidByIdServiceException;
use App\Modules\Shared\ValueObject\EmailId;

interface UpdateCheckedAndValidByIdServiceInterface
{
    /**
     * @throws UpdateCheckedAndValidByIdServiceException
     */
    public function handle(
        EmailId $id,
        bool $isChecked,
        bool $isValid
    ): bool;
}
