<?php

declare(strict_types=1);

namespace App\Core\Database\Connection;

use PDO;

class GetDatabaseConnection implements GetDatabaseConnectionInterface
{
    private static ?PDO $connection = null;

    public function __construct(
        private readonly string $host,
        private readonly string $database,
        private readonly string $port,
        private readonly string $charset,
        private readonly string $user,
        private readonly string $password,
    ) {
    }

    public function handle(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;port=%d;charset=%s',
            $this->host,
            $this->database,
            $this->port,
            $this->charset
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $pdo = new PDO($dsn, $this->user, $this->password, $options);

        self::$connection = $pdo;

        return $pdo;
    }
}