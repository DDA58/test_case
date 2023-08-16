<?php

declare(strict_types=1);

namespace App\Modules\Notify\Repository\EmailsSendLog;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use App\Modules\Notify\Dto\SaveEmailsSendLogDto;

readonly class EmailsSendLogRepository implements EmailsSendLogRepositoryInterface
{
    public function __construct(
        private GetDatabaseConnectionInterface $getDatabaseConnection
    ) {
    }

    public function save(SaveEmailsSendLogDto $dto): bool
    {
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
    }
}