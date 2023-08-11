<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use App\Enum\CommandsExecutionLogStatusEnum;
use PDO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * TODO Move to supervisor?
 */
#[AsCommand(name: 'notify_worker:by_processes')]
class SimpleByProcessesNotifyWorkerCommand extends Command
{
    private const MAX_THREADS = 50;

    private const PARENT_COMMAND_ID_OPTION = 'parent_command_id';

    protected function configure(): void
    {
        $this
            ->addOption(self::PARENT_COMMAND_ID_OPTION, null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parentCommandId = (int)$input->getOption(self::PARENT_COMMAND_ID_OPTION);

        if ($parentCommandId === 0) {
            return Command::INVALID;
        }

        $connection = GetDatabaseConnection::getInstance();
        $connection->beginTransaction();
        $statement = $connection->prepare(sprintf(
            'SELECT id, `command`
            FROM commands_queue
            WHERE parent_command_id = ?
                AND `status` = ?
            LIMIT %d
            FOR UPDATE',
            self::MAX_THREADS
        ));
        $statement->execute([
            $parentCommandId,
            CommandsExecutionLogStatusEnum::Created->value
        ]);

        $commands = $statement->fetchAll();

        $processes = [];

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline($command['command'], APP_PATH);
            $process->setTimeout(0);
            $process->disableOutput();
            $process->start();
            $processes[$command['id']] = $process;
            $statement = $connection->prepare(
                'UPDATE commands_queue
            SET `status` = ?
            , command_pid = ?
            WHERE id = ?'
            );
            $statement->execute([
                CommandsExecutionLogStatusEnum::Started->value,
                $process->getPid(),
                $command['id']
            ]);
        }

        $connection->commit();

        while (count($processes)) {
            foreach ($processes as $commandId => $runningProcess) {
                if (count($processes) > self::MAX_THREADS || $runningProcess->isRunning() === true) {
                    usleep(10000);

                    continue;
                }

                if ($runningProcess->isRunning() === false) {
                    unset($processes[$commandId]);

                    $statement = $connection->prepare(
                        'UPDATE commands_queue SET `status` = ? WHERE id = ?'
                    );
                    $statement->execute([
                        $runningProcess->getExitCode() ? CommandsExecutionLogStatusEnum::Failed->value : CommandsExecutionLogStatusEnum::Success->value,
                        $commandId
                    ]);
                }

                $connection->beginTransaction();

                $statement = $connection->prepare(
                    'SELECT id, `command`
        FROM commands_queue
        WHERE parent_command_id = ?
            AND `status` = ?
        LIMIT 1
        FOR UPDATE'
                );
                $statement->execute([
                    $parentCommandId,
                    CommandsExecutionLogStatusEnum::Created->value
                ]);

                $newCommand = $statement->fetch(PDO::FETCH_OBJ);

                if (!$newCommand) {
                    $connection->commit();

                    break;
                }

                $process = Process::fromShellCommandline($newCommand->command, APP_PATH);
                $process->setTimeout(0);
                $process->disableOutput();
                $process->start();
                $processes[$newCommand->id] = $process;
                $statement = $connection->prepare(
                    'UPDATE commands_queue
            SET `status` = ?
            , command_pid = ?
            WHERE id = ?'
                );
                $statement->execute([
                    CommandsExecutionLogStatusEnum::Started->value,
                    $process->getPid(),
                    $newCommand->id
                ]);

                $connection->commit();
            }
        }

        return Command::SUCCESS;
    }
}
