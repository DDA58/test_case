<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\SaveEmailsSendLog;

use App\Modules\Notify\Dto\SaveEmailsSendLogDto;
use App\Modules\Notify\Service\SaveEmailsSendLog\Exception\SaveEmailsSendLogServiceException;

interface SaveEmailsSendLogServiceInterface
{
    /**
     * @throws SaveEmailsSendLogServiceException
     */
    public function handle(SaveEmailsSendLogDto $dto): bool;
}
