<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\SaveEmailsSendLog;

use App\Modules\Notify\Dto\SaveEmailsSendLogDto;
use App\Modules\Notify\Repository\EmailsSendLog\EmailsSendLogRepository;
use App\Modules\Notify\Repository\EmailsSendLog\Exception\EmailsSendLogRepositoryException;
use App\Modules\Notify\Service\SaveEmailsSendLog\Exception\SaveEmailsSendLogServiceException;

readonly class SaveEmailsSendLogService implements SaveEmailsSendLogServiceInterface
{
    public function __construct(
        private EmailsSendLogRepository $emailsSendLogRepository
    ) {
    }

    public function handle(SaveEmailsSendLogDto $dto): bool
    {
        try {
            return $this->emailsSendLogRepository->save($dto);
        } catch (EmailsSendLogRepositoryException $exception) {
            throw new SaveEmailsSendLogServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
