<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Dotenv\Dotenv;

class App extends Application
{
    private const ENV_FILE_NOT_FOUND = '[App] File .env not found';

    /**
     * @param iterable<Command> $commands
     */
    public function __construct(
        iterable $commands,
        string $appPath,
        string $name = 'UNKNOWN',
        string $version = 'UNKNOWN'
    ) {
        $envFile = $appPath . '/.env';

        if(!file_exists($envFile)) {
            throw new RuntimeException(self::ENV_FILE_NOT_FOUND);
        }

        (new Dotenv())->load($envFile);

        foreach ($commands as $command) {
            $this->add($command);
        }

        parent::__construct($name, $version);
    }
}
