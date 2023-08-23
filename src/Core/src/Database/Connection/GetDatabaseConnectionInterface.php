<?php

declare(strict_types=1);

namespace App\Core\Database\Connection;

use PDO;

interface GetDatabaseConnectionInterface
{
    public function handle(): PDO;
}
