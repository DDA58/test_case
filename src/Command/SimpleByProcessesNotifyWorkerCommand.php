<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use App\Enum\CommandsExecutionLogStatusEnum;
use App\Enum\DefaultExitCodesEnum;
use PDO;
use Symfony\Component\Process\Process;

/**
 * TODO Move to supervisor?
 */
class SimpleByProcessesNotifyWorkerCommand implements CommandInterface
{
    private const MAX_THREADS = 50;

    private const PARENT_COMMAND_ID_OPTION = 'parent_command_id';

    private const NAME = 'notify_worker:by_processes';

    public static function getName(): string
    {
        return self::NAME;
    }

    public function handle(array $arguments = []): int
    {
        $parentCommandId = 0;

        foreach ($arguments as $arg) {
            if (str_starts_with($arg, '--' . self::PARENT_COMMAND_ID_OPTION) === true) {
                $parentCommandId = (int)$this->extractOptionValue($arg, self::PARENT_COMMAND_ID_OPTION);

                break;
            }
        }

        if ($parentCommandId === 0) {
            return DefaultExitCodesEnum::Invalid->value;
        }

        $connection = GetDatabaseConnection::getInstance();
        $statement = $connection->prepare(sprintf(
            'SELECT id, `command`
            FROM commands_queue
            WHERE parent_command_id = ?
                AND `status` = ?
            LIMIT %d',
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
            $processes[] = $process;
        }

        while (count($processes)) {
            foreach ($processes as $i => $runningProcess) {
                if (count($processes) > self::MAX_THREADS || $runningProcess->isRunning() === true) {
                    usleep(10000);

                    continue;
                }

                if ($runningProcess->isRunning() === false) {
                    unset($processes[$i]);
                }

                $statement = $connection->prepare(
                    'SELECT id, `command`
        FROM commands_queue
        WHERE parent_command_id = ?
            AND `status` = ?
        LIMIT 1'
                );
                $statement->execute([
                    $parentCommandId,
                    CommandsExecutionLogStatusEnum::Created->value
                ]);

                $newCommand = $statement->fetch(PDO::FETCH_OBJ);

                if (!$newCommand) {
                    break 2;
                }

                $process = Process::fromShellCommandline($newCommand->command, APP_PATH);
                $process->setTimeout(0);
                $process->disableOutput();
                $process->start();
                $processes[] = $process;
            }
        }

        return DefaultExitCodesEnum::Success->value;
    }

    private function extractOptionValue(string $value, string $optionName): string
    {
        return str_replace('--' . $optionName . '=', '', $value);
    }
}
