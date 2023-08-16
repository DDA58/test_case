<?php

declare(strict_types=1);

namespace App\Modules\Notify\Repository\EmailForNotify;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use App\Modules\Notify\Dto\EmailForNotifyDto;
use App\Modules\Shared\ValueObject\EmailId;
use PDO;

readonly class EmailForNotifyRepository implements EmailForNotifyRepositoryInterface
{
    public function __construct(
        private GetDatabaseConnectionInterface $getDatabaseConnection
    ) {
    }

    public function findByEmailId(
        EmailId $emailId,
        bool $forUpdate = false
    ): ?EmailForNotifyDto {
        $statement = $this->getDatabaseConnection->handle()->prepare(
            'SELECT users.`uuid` AS `user_uuid`
, users.username
, emails.id AS `email_id`
, emails.email
, emails.confirmed AS `email_confirmed`
, emails.`checked` AS `email_checked`
, emails.valid AS `email_valid`
FROM emails
JOIN users ON emails.user_uuid = users.uuid AND emails.is_last = 1
WHERE emails.id = ?
LIMIT 1' . ($forUpdate ? ' FOR UPDATE' : '')
        );
        $statement->execute([$emailId->getValue()]);

        $email = $statement->fetch(PDO::FETCH_OBJ);

        if ($email === false) {
            return null;
        }

        return new EmailForNotifyDto(
            $email->user_uuid,
            $email->username,
            $email->email_id,
            $email->email,
            (bool)$email->email_confirmed,
            (bool)$email->email_checked,
            (bool)$email->email_valid,
        );
    }
}