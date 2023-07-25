<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use App\Enum\CommandsExecutionLogStatusEnum;
use App\Enum\DefaultExitCodesEnum;
use App\Iterator\GetEmailIdsWithExpiredSubscriptionIterator;
use PDOException;

class InitAfterExpirationCommand implements CommandInterface
{
    private const EMAILS_PER_COMMAND = 10;

    private const NAME = 'init:after_expiration';

    public static function getName(): string
    {
        return self::NAME;
    }

    public function handle(array $arguments = []): int
    {
        $connection = GetDatabaseConnection::getInstance();
        $statement = $connection->prepare(
            'INSERT INTO commands_queue(`command`, `command_pid`, `parent_command_id`, `status`) VALUES(?, ?, NULL, ?)'
        );
        $statement->execute([
            implode(' ', ['php', ...$arguments]),
            getmypid(),
            CommandsExecutionLogStatusEnum::Started->value
        ]);
        $id = (int)$connection->lastInsertId();

        //TODO Split into several packages with fixed amount rows
        $query = 'INSERT INTO commands_queue(`command`, `command_pid`, `parent_command_id`, `status`) VALUES';
        $pureCommand = sprintf('%s %s/src/main.php %s --parent_command_id=%d --email_ids=', PHP_BINARY, APP_PATH, NotifyAfterSubscriptionExpiredCommand::getName(), $id);
        $command = $pureCommand;
        $index = 0;
        $valuesWereAdded = false;

        foreach (new GetEmailIdsWithExpiredSubscriptionIterator() as $i => $row) {
            if ($index === self::EMAILS_PER_COMMAND) {
                $query .= sprintf(($i === self::EMAILS_PER_COMMAND ? '' : ',') . '("%s", NULL, %d, "%s")', $command, $id, CommandsExecutionLogStatusEnum::Created->value);

                $index = 0;
                $command = $pureCommand;
                $valuesWereAdded = true;
            }

            $command .= ($index === 0 ? '' : ',') . $row->email_id;
            $index++;
        }

        if ($index > 0) {
            $query .= sprintf(($valuesWereAdded ? ',' : '') . '("%s", NULL, %d, "%s")', $command, $id, CommandsExecutionLogStatusEnum::Created->value);

            $valuesWereAdded = true;
        }

        if ($valuesWereAdded === false) {
            return DefaultExitCodesEnum::Success->value;
        }

        $status = CommandsExecutionLogStatusEnum::Failed;
        $connection->beginTransaction();

        try {
            $connection->query($query);

            $connection->commit();

            $status = CommandsExecutionLogStatusEnum::Success;
        } catch (PDOException $exception) {
            $connection->rollBack();

            return DefaultExitCodesEnum::Fail->value;
        } finally {
            $statement = $connection->prepare(
                'UPDATE commands_queue SET `status` = ? WHERE id = ?'
            );
            $statement->execute([
                $status->value,
                $id
            ]);
        }

        $this->startWorker($id);

        return DefaultExitCodesEnum::Success->value;
    }

    private function startWorker(
        int $parentCommandId
    ): void {
        exec(
            sprintf(
                '%s %s/src/main.php %s --parent_command_id=%d > /dev/null 2>&1 &',
                PHP_BINARY,
                APP_PATH,
                SimpleByProcessesNotifyWorkerCommand::getName(), //SimpleByDatabaseNotifyWorkerCommand
                $parentCommandId
            )
        );
    }
}
