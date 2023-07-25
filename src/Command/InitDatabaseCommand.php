<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use App\Enum\DefaultExitCodesEnum;

class InitDatabaseCommand implements CommandInterface
{
    private const NAME = 'init:database';

    public static function getName(): string
    {
        return self::NAME;
    }

    public function handle(array $arguments = []): int
    {
        $connection = GetDatabaseConnection::getInstance();

        echo '[InitDatabaseCommand] Start making skeleton' . PHP_EOL;

        $connection->query(
            file_get_contents(sprintf('%s/database/skeleton.sql', APP_PATH))
        );

        echo '[InitDatabaseCommand] Finish making skeleton' . PHP_EOL;

        echo '[InitDatabaseCommand] Start seeding' . PHP_EOL;

        $connection->query(
            file_get_contents(sprintf('%s/database/seeds.sql', APP_PATH))
        );

        echo '[InitDatabaseCommand] End seeding' . PHP_EOL;

        return DefaultExitCodesEnum::Success->value;
    }
}
