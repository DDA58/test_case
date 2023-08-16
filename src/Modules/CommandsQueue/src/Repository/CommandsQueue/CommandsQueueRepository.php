<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Repository\CommandsQueue;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Entity\CommandsQueueEntity;
use App\Modules\Notify\Dto\EmailForNotifyDto;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\EmailId;
use DateTimeImmutable;
use PDO;

readonly class CommandsQueueRepository implements CommandsQueueRepositoryInterface
{
    public function __construct(
        private GetDatabaseConnectionInterface $getDatabaseConnection
    ) {
    }

    public function save(SaveCommandsQueueDto $saveCommandsQueueDto): int
    {
        $connection = $this->getDatabaseConnection->handle();

        $statement = $connection->prepare(
            'INSERT INTO commands_queue(`command`, `command_pid`, `parent_command_id`, `status`) VALUES(?, ?, ?, ?)'
        );
        $statement->execute([
            $saveCommandsQueueDto->getCommand(),
            $saveCommandsQueueDto->getCommandPid(),
            $saveCommandsQueueDto->getParentCommandId(),
            $saveCommandsQueueDto->getStatus()->value
        ]);

        //TODO check before casts on types
        return (int)$connection->lastInsertId();
    }

    /**
     * @inheritDoc
     */
    public function bulkSave(iterable $commands): bool
    {
        $connection = $this->getDatabaseConnection->handle();

        $placeholders = '';
        $params = [];

        foreach ($commands as $saveCommandsQueueDto) {
            $placeholders .= '(?, ?, ?, ?),';
            $params[] = $saveCommandsQueueDto->getCommand();
            $params[] = $saveCommandsQueueDto->getCommandPid();
            $params[] = $saveCommandsQueueDto->getParentCommandId();
            $params[] = $saveCommandsQueueDto->getStatus()->value;
        }

        if ($params === []) {
            return true;
        }

        $placeholders = rtrim($placeholders, ',');

        $statement = $connection->prepare(
            'INSERT INTO commands_queue(`command`, `command_pid`, `parent_command_id`, `status`) VALUES' . $placeholders
        );

        return $statement->execute($params);
    }

    public function concatCommandIdToColumnByParentCommandId(int $parentCommandId): bool
    {
        $connection = $this->getDatabaseConnection->handle();

        $statement = $connection->prepare(
            'UPDATE commands_queue
            SET `command` = CONCAT(`command`, " --command_id=", `id`)
            WHERE parent_command_id = ?'
        );

        return $statement->execute([$parentCommandId]);
    }

    public function updateStatusByParentCommandId(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status
    ): bool {
        $connection = $this->getDatabaseConnection->handle();

        $statement = $connection->prepare(
            'UPDATE commands_queue
            SET `status` = ?
            WHERE parent_command_id = ?'
        );

        return $statement->execute([$status->value, $parentCommandId]);
    }

    public function updateStatusByCommandId(
        int $commandId,
        CommandsExecutionLogStatusEnum $status
    ): bool {
        $connection = $this->getDatabaseConnection->handle();

        $statement = $connection->prepare(
            'UPDATE commands_queue
            SET `status` = ?
            WHERE id = ?'
        );

        return $statement->execute([$status->value, $commandId]);
    }

    /**
     * @inheritDoc
     */
    public function getByParentCommandIdAndStatus(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        int $limit,
        bool $forUpdate = false
    ): iterable {
        $connection = $this->getDatabaseConnection->handle();

        $statement = $connection->prepare(sprintf(
            'SELECT id, `command`, command_pid, created_at
            FROM commands_queue
            WHERE parent_command_id = ?
                AND `status` = ?
            LIMIT %d' . ($forUpdate ? ' FOR UPDATE' : ''),
            $limit
        ));
        $statement->execute([
            $parentCommandId,
            $status->value
        ]);

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            yield new CommandsQueueEntity(
                $row->id,
                $row->command,
                $row->command_pid,
                $parentCommandId,
                $status,
                new DateTimeImmutable($row->created_at)
            );
        }
    }

    public function updateStatusAndCommandPidByCommandId(
        int $commandId,
        CommandsExecutionLogStatusEnum $status,
        ?int $commandPid,
    ): bool {
        $connection = $this->getDatabaseConnection->handle();

        $statement = $connection->prepare(
            'UPDATE commands_queue
            SET `status` = ?
            , command_pid = ?
            WHERE id = ?'
        );

        return $statement->execute([$status->value, $commandPid, $commandId]);
    }

    public function findByParentCommandIdAndStatus(
        int $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        bool $forUpdate = false
    ): ?CommandsQueueEntity {
        $commands = $this->getByParentCommandIdAndStatus($parentCommandId, $status, 1, $forUpdate);

        foreach ($commands as $command) {
            return $command;
        }

        return null;
    }
}