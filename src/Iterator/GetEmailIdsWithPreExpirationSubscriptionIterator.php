<?php

declare(strict_types=1);

namespace App\Iterator;

use App\Core\Database\GetDatabaseConnection;
use Iterator;
use PDO;

class GetEmailIdsWithPreExpirationSubscriptionIterator implements Iterator
{
    private mixed $row;
    private int $index = 0;
    private mixed $statement;

    public function __construct(
        private readonly int $daysBeforeExpiration
    ) {
    }

    public function current(): mixed
    {
        return $this->row;
    }

    public function next(): void
    {
        $this->row = $this->statement->fetch(PDO::FETCH_OBJ);
        $this->index++;
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return !empty($this->row);
    }

    public function rewind(): void
    {
        $this->statement = GetDatabaseConnection::getInstance()->prepare(
            'SELECT emails.id as `email_id`
FROM users
JOIN emails FORCE INDEX FOR JOIN (`emails_user_uuid_IDX`) ON emails.user_uuid = users.uuid AND emails.is_last = 1
WHERE validts > 0
AND DATEDIFF(CURDATE(), FROM_UNIXTIME(validts)) = ?'
        );
        $this->statement->execute([
            '-' . $this->daysBeforeExpiration
        ]);

        $this->row = $this->statement->fetch(PDO::FETCH_OBJ);
    }
}
