<?php

declare(strict_types=1);

namespace App\Modules\Email\Service\UpdateCheckedAndValidById;

use App\Modules\Shared\ValueObject\EmailId;

interface UpdateCheckedAndValidByIdServiceInterface
{
    public function handle(
        EmailId $id,
        bool $isChecked,
        bool $isValid
    ): bool;
}