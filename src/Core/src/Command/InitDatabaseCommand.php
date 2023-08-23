<?php

declare(strict_types=1);

namespace App\Core\Command;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use FilesystemIterator;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'init:database')]
class InitDatabaseCommand extends Command
{
    public function __construct(
        private readonly GetDatabaseConnectionInterface $getDatabaseConnection,
        private readonly string $appPath,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $connection = $this->getDatabaseConnection->handle();

        $output->writeln('[InitDatabaseCommand] Start making skeleton');

        $filesPaths = [];

        /** @var SplFileInfo $fileInfo */
        foreach (new FilesystemIterator(sprintf('%s/database/skeleton', $this->appPath), FilesystemIterator::CURRENT_AS_SELF | FilesystemIterator::SKIP_DOTS) as $fileInfo) {
            $path = $fileInfo->getRealPath();

            if ($path !== false) {
                $filesPaths[] = $path;
            }
        }

        sort($filesPaths);

        foreach ($filesPaths as $filePath) {
            $connection->query(file_get_contents($filePath));
        }

        $output->writeln('[InitDatabaseCommand] Finish making skeleton');

        $output->writeln('[InitDatabaseCommand] Start seeding');

        $connection->query(
            file_get_contents(sprintf('%s/database/seeds.sql', $this->appPath))
        );

        $output->writeln('[InitDatabaseCommand] End seeding');

        return Command::SUCCESS;
    }
}
