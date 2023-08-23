<?php

declare(strict_types=1);

namespace App\Modules\Notify\Repository\EmailsSendLog;

use App\Modules\Notify\Dto\SaveEmailsSendLogDto;
use App\Modules\Notify\Repository\EmailsSendLog\Exception\EmailsSendLogRepositoryException;

interface EmailsSendLogRepositoryInterface
{
    /**
     * @throws EmailsSendLogRepositoryException
     */
    public function save(SaveEmailsSendLogDto $dto): bool;
}
