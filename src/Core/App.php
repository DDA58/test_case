<?php

declare(strict_types=1);

namespace App\Core;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class App extends Application
{
    /**
     * @param iterable<Command> $commands
     */
    public function __construct(
        iterable $commands,
        string $name = 'UNKNOWN',
        string $version = 'UNKNOWN'
    ) {
        foreach ($commands as $command) {
            $this->add($command);
        }

        parent::__construct($name, $version);
    }
}
