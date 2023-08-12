<?php

declare(strict_types=1);

namespace App\Core;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Dotenv\Dotenv;

class App extends Application
{
    /**
     * @param iterable<Command> $commands
     */
    public function __construct(
        iterable $commands,
        string $appPath,
        string $name = 'UNKNOWN',
        string $version = 'UNKNOWN'
    ) {
        (new Dotenv())->load($appPath . '/.env');

        foreach ($commands as $command) {
            $this->add($command);
        }

        parent::__construct($name, $version);
    }
}
