<?php

declare(strict_types=1);

namespace App\Modules\Email\Repository\Email;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use App\Modules\Email\Dto\EmailWithExpiredSubscriptionDto;
use App\Modules\Email\Dto\EmailWithPreExpirationSubscriptionDto;
use App\Modules\Email\Repository\Email\Exception\EmailRepositoryException;
use App\Modules\Shared\ValueObject\EmailId;
use PDO;
use Throwable;

readonly class EmailRepository implements EmailRepositoryInterface
{
    public function __construct(
        private GetDatabaseConnectionInterface $getDatabaseConnection
    ) {
    }

    public function getEmailsWithExpiredSubscription(): iterable
    {
        try {
            $statement = $this->getDatabaseConnection->handle()->prepare(
                'SELECT emails.id as `email_id`
FROM users
JOIN emails FORCE INDEX FOR JOIN (`emails_user_uuid_IDX`) ON emails.user_uuid = users.uuid AND emails.is_last = 1
WHERE validts > 0
AND DATEDIFF(CURDATE(), FROM_UNIXTIME(validts)) > 0'
            );
            $statement->execute();

            while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
                yield new EmailWithExpiredSubscriptionDto((int)$row->email_id);
            }
        } catch (Throwable $t) {
            throw new EmailRepositoryException($t->getMessage(), (int)$t->getCode(), $t);
        }
    }

    public function getEmailsWithPreExpirationSubscription(int $daysBeforeExpiration): iterable
    {
        try {
            $statement = $this->getDatabaseConnection->handle()->prepare(
                'SELECT emails.id as `email_id`
FROM users
JOIN emails FORCE INDEX FOR JOIN (`emails_user_uuid_IDX`) ON emails.user_uuid = users.uuid AND emails.is_last = 1
WHERE validts > 0
AND DATEDIFF(CURDATE(), FROM_UNIXTIME(validts)) = ?'
            );
            $statement->execute([
                '-' . $daysBeforeExpiration
            ]);

            while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
                yield new EmailWithPreExpirationSubscriptionDto((int)$row->email_id);
            }
        } catch (Throwable $t) {
            throw new EmailRepositoryException($t->getMessage(), (int)$t->getCode(), $t);
        }
    }

    public function updateCheckedAndValidById(
        EmailId $id,
        bool $isChecked,
        bool $isValid
    ): bool {
        try {
            $statement = $this->getDatabaseConnection->handle()->prepare(
                'UPDATE emails
        SET `checked` = ?,
            `valid` = ?
        WHERE id = ?'
            );

            return $statement->execute([
                (int)$isChecked,
                (int)$isValid,
                $id->getValue(),
            ]);
        } catch (Throwable $t) {
            throw new EmailRepositoryException($t->getMessage(), (int)$t->getCode(), $t);
        }
    }
}
