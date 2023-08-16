<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\SaveEmailsSendLog;

use App\Modules\Notify\Dto\SaveEmailsSendLogDto;
use App\Modules\Notify\Repository\EmailsSendLog\EmailsSendLogRepository;

readonly class SaveEmailsSendLogService implements SaveEmailsSendLogServiceInterface
{
    public function __construct(
        private EmailsSendLogRepository $emailsSendLogRepository
    ) {
    }

    public function handle(SaveEmailsSendLogDto $dto): bool
    {
        return $this->emailsSendLogRepository->save($dto);
    }
}