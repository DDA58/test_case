<?php

declare(strict_types=1);

namespace App\Core\Database;

use PDO;
use RuntimeException;

class GetDatabaseConnection
{
    private const INCORRECT_ENV_EXCEPTION_MESSAGE = '[GetDatabaseConnection] Incorrect env';

    private static ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function getInstance(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        if (isset($_ENV['MYSQL_HOST'], $_ENV['MYSQL_DATABASE'], $_ENV['MYSQL_PORT'], $_ENV['MYSQL_CHARSET'], $_ENV['MYSQL_ROOT_PASSWORD']) === false) {
            throw new RuntimeException(self::INCORRECT_ENV_EXCEPTION_MESSAGE);
        }

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;port=%d;charset=%s',
            $_ENV['MYSQL_HOST'],
            $_ENV['MYSQL_DATABASE'],
            $_ENV['MYSQL_PORT'],
            $_ENV['MYSQL_CHARSET']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $pdo = new PDO($dsn, 'root', $_ENV['MYSQL_ROOT_PASSWORD'], $options);

        self::$connection = $pdo;

        return $pdo;
    }
}
