<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use App\Core\Database\GetDatabaseConnection;
use FilesystemIterator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'init:database')]
class InitDatabaseCommand extends Command
{
    public function __construct(
        private readonly GetDatabaseConnectionInterface $getDatabaseConnection,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->getDatabaseConnection->handle();

        $output->writeln('[InitDatabaseCommand] Start making skeleton');

        $filesPaths = [];

        foreach (new FilesystemIterator(sprintf('%s/database/skeleton', APP_PATH), FilesystemIterator::CURRENT_AS_SELF | FilesystemIterator::SKIP_DOTS) as $fileInfo) {
            $filesPaths[] = $fileInfo->getRealPath();
        }

        sort($filesPaths);

        foreach ($filesPaths as $filePath) {
            $connection->query(file_get_contents($filePath));
        }

        $output->writeln('[InitDatabaseCommand] Finish making skeleton');

        $output->writeln('[InitDatabaseCommand] Start seeding');

        $connection->query(
            file_get_contents(sprintf('%s/database/seeds.sql', APP_PATH))
        );

        $output->writeln('[InitDatabaseCommand] End seeding');

        return Command::SUCCESS;
    }
}
