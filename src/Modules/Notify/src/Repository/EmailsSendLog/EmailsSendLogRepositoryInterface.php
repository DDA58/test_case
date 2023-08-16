<?php

declare(strict_types=1);

namespace App\Modules\Notify\Repository\EmailsSendLog;

use App\Modules\Notify\Dto\SaveEmailsSendLogDto;

interface EmailsSendLogRepositoryInterface
{
    public function save(SaveEmailsSendLogDto $dto): bool;
}