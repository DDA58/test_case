<?php

declare(strict_types=1);

namespace App\Modules\Notify\Repository\EmailsSendLog;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use App\Modules\Notify\Dto\SaveEmailsSendLogDto;
use App\Modules\Notify\Repository\EmailsSendLog\Exception\EmailsSendLogRepositoryException;
use Throwable;

readonly class EmailsSendLogRepository implements EmailsSendLogRepositoryInterface
{
    public function __construct(
        private GetDatabaseConnectionInterface $getDatabaseConnection
    ) {
    }

    public function save(SaveEmailsSendLogDto $dto): bool
    {
        try {
            $statement = $this->getDatabaseConnection->handle()->prepare(
                'INSERT INTO emails_send_log(`type`, command_id, email_id, confirmed, checked, valid) VALUES(?, ?, ?, ?, ?, ?);'
            );

            return $statement->execute([
                $dto->getType()->value,
                $dto->getCommandId(),
                $dto->getEmailId(),
                (int)$dto->isEmailConfirmed(),
                (int)$dto->isEmailChecked(),
                (int)$dto->isEmailValid(),
            ]);
        } catch (Throwable $throwable) {
            throw new EmailsSendLogRepositoryException($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }
}
