<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use App\Enum\CommandsExecutionLogStatusEnum;
use App\Enum\DefaultExitCodesEnum;
use PDO;

/**
 * TODO Move to supervisor?
 */
class SimpleByDatabaseNotifyWorkerCommand implements CommandInterface
{
    private const PARENT_COMMAND_ID_OPTION = 'parent_command_id';

    private const NAME = 'notify_worker:by_database';

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
        $statement = $connection->prepare(
            'SELECT id, `command`
            FROM commands_queue
            WHERE parent_command_id = ?
                AND `status` = ?
            LIMIT 50'
        );
        $statement->execute([
            $parentCommandId,
            CommandsExecutionLogStatusEnum::Created->value
        ]);

        $commands = $statement->fetchAll();
        $ids = array_column($commands, 'id');

        foreach ($commands as $command) {
            exec($command['command'] . ' > /dev/null 2>&1 &');
        }

        while (true) {
            foreach ($ids as $index => $id) {
                $statement = $connection->prepare(
                    'SELECT *
            FROM commands_queue
            WHERE id = ?'
                );
                $statement->execute([
                    $id
                ]);

                $command = $statement->fetch(PDO::FETCH_OBJ);

                if (
                    in_array($command->status, [
                        CommandsExecutionLogStatusEnum::Success->value,
                        CommandsExecutionLogStatusEnum::Failed->value
                    ]) === false
                ) {
                    continue;
                }

                unset($ids[$index]);

                /**
                 * TODO make if() with random start garbage collector for $ids
                 * Db select with current ids
                 * Check finished commands
                 * Remove from $ids
                 * Fill $ids several new values
                 */

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

                $ids[] = $newCommand->id;

                exec($newCommand->command . ' > /dev/null 2>&1 &');
            }
        }

        return DefaultExitCodesEnum::Success->value;
    }

    private function extractOptionValue(string $value, string $optionName): string
    {
        return str_replace('--' . $optionName . '=', '', $value);
    }
}
