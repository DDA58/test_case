<?php

declare(strict_types=1);

namespace App\Email\Repository\Email;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use App\Email\Dto\EmailWithExpiredSubscriptionDto;
use App\Email\Dto\EmailWithPreExpirationSubscriptionDto;
use PDO;

readonly class EmailRepository implements EmailRepositoryInterface
{
    public function __construct(
        private GetDatabaseConnectionInterface $getDatabaseConnection
    ) {
    }

    public function getEmailsWithExpiredSubscription(): iterable
    {
        $statement = $this->getDatabaseConnection->handle()->prepare(
            'SELECT emails.id as `email_id`
FROM users
JOIN emails FORCE INDEX FOR JOIN (`emails_user_uuid_IDX`) ON emails.user_uuid = users.uuid AND emails.is_last = 1
WHERE validts > 0
AND DATEDIFF(CURDATE(), FROM_UNIXTIME(validts)) > 0'
        );
        $statement->execute();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            yield new EmailWithExpiredSubscriptionDto($row->email_id);
        }
    }

    public function getEmailsWithPreExpirationSubscription(int $daysBeforeExpiration): iterable
    {
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
            yield new EmailWithPreExpirationSubscriptionDto($row->email_id);
        }
    }
}