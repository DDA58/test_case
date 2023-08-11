<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use App\Enum\CommandsExecutionLogStatusEnum;
use App\Iterator\GetEmailIdsWithExpiredSubscriptionIterator;
use PDOException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'fill_commands_queue:after_expiration')]
class FillCommandsQueueAfterExpirationCommand extends Command
{
    private const EMAILS_PER_COMMAND = 10;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $arguments = $input->getArguments();

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
        $pureCommand = sprintf('%s %s/bin/console %s --email_ids=', PHP_BINARY, APP_PATH, NotifyAfterSubscriptionExpiredCommand::getDefaultName());
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
            return Command::SUCCESS;
        }

        $status = CommandsExecutionLogStatusEnum::Failed;
        $connection->beginTransaction();

        try {
            $connection->query($query);

            $statement = $connection->prepare(
                'UPDATE commands_queue
            SET `command` = CONCAT(`command`, " --command_id=", `id`)
            WHERE parent_command_id = ?'
            );
            $statement->execute([$id]);

            $connection->commit();

            $status = CommandsExecutionLogStatusEnum::Success;
        } catch (PDOException $exception) {
            $connection->rollBack();

            return Command::FAILURE;
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

        return Command::SUCCESS;
    }

    private function startWorker(
        int $parentCommandId
    ): void {
        exec(
            sprintf(
                '%s %s/bin/console %s --parent_command_id=%d > /dev/null 2>&1 &',
                PHP_BINARY,
                APP_PATH,
                SimpleByProcessesNotifyWorkerCommand::getDefaultName(), //SimpleByDatabaseNotifyWorkerCommand
                $parentCommandId
            )
        );
    }
}
