<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use App\Email\Service\GetEmailsWithPreExpirationSubscription\GetEmailsWithPreExpirationSubscriptionServiceInterface;
use App\Enum\CommandsExecutionLogStatusEnum;
use PDOException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'fill_commands_queue:before_expiration')]
class FillCommandsQueueBeforeExpirationCommand extends Command
{
    private const DAYS_BEFORE_EXPIRATION = 'days_before_expiration';
    private const EMAILS_PER_COMMAND = 'emails_per_command';

    public function __construct(
        private readonly GetEmailsWithPreExpirationSubscriptionServiceInterface $getEmailsWithPreExpirationSubscriptionService,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption(self::DAYS_BEFORE_EXPIRATION, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::EMAILS_PER_COMMAND, null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $daysBeforeExpiration = (int)$input->getOption(self::DAYS_BEFORE_EXPIRATION);
        $emailsPerCommand = (int)$input->getOption(self::EMAILS_PER_COMMAND);

        if ($daysBeforeExpiration <= 0 || $emailsPerCommand <= 0) {
            return Command::INVALID;
        }

        $arguments = $_SERVER['argv'];
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

        $emailsBeforeExpiration = $this->addBeforeExpiration(
            $id,
            $daysBeforeExpiration,
            NotifyBeforeSubscriptionExpirationCommand::getDefaultName(),
            $emailsPerCommand
        );

        $valuesWereAdded = $emailsBeforeExpiration['valuesWereAdded'];

        try {
            $status = CommandsExecutionLogStatusEnum::Success;

            if ($valuesWereAdded === false) {
                return Command::SUCCESS;
            }

            $query = $emailsBeforeExpiration['query'];

            $status = CommandsExecutionLogStatusEnum::Failed;
            $connection->beginTransaction();

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

    private function addBeforeExpiration(
        int $parentCommandId,
        int $daysBeforeExpiration,
        string $commandName,
        int $emailsPerCommand
    ): array {
        //TODO Split into several packages with fixed amount rows
        $query = 'INSERT INTO commands_queue(`command`, `command_pid`, `parent_command_id`, `status`) VALUES';
        $pureCommand = sprintf('%s %s/bin/console %s --days_before_expiration=%d --email_ids=', PHP_BINARY, APP_PATH, $commandName, $daysBeforeExpiration);
        $command = $pureCommand;
        $index = 0;
        $valuesWereAdded = false;

        foreach ($this->getEmailsWithPreExpirationSubscriptionService->handle($daysBeforeExpiration) as $i => $row) {
            if ($index === $emailsPerCommand) {
                $query .= sprintf(($i === $emailsPerCommand ? '' : ',') . '("%s", NULL, %d, "%s")', $command, $parentCommandId, CommandsExecutionLogStatusEnum::Created->value);

                $index = 0;
                $command = $pureCommand;
                $valuesWereAdded = true;
            }

            $command .= ($index === 0 ? '' : ',') . $row->getEmailId();
            $index++;
        }

        if ($index > 0) {
            $query .= sprintf(($valuesWereAdded ? ',' : '') . '("%s", NULL, %d, "%s")', $command, $parentCommandId, CommandsExecutionLogStatusEnum::Created->value);

            $valuesWereAdded = true;
        }

        return [
            'query' => $valuesWereAdded ? $query . ';' : '',
            'valuesWereAdded' => $valuesWereAdded,
        ];
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
