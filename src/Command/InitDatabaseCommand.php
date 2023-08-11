<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'init:database')]
class InitDatabaseCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = GetDatabaseConnection::getInstance();

        $output->writeln('[InitDatabaseCommand] Start making skeleton');

        $connection->query(
            file_get_contents(sprintf('%s/database/skeleton.sql', APP_PATH))
        );

        $output->writeln('[InitDatabaseCommand] Finish making skeleton');

        $output->writeln('[InitDatabaseCommand] Start seeding');

        $connection->query(
            file_get_contents(sprintf('%s/database/seeds.sql', APP_PATH))
        );

        $output->writeln('[InitDatabaseCommand] End seeding');

        return Command::SUCCESS;
    }
}
