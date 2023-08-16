<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\SaveEmailsSendLog;

use App\Modules\Notify\Dto\SaveEmailsSendLogDto;

interface SaveEmailsSendLogServiceInterface
{
    public function handle(SaveEmailsSendLogDto $dto): bool;
}