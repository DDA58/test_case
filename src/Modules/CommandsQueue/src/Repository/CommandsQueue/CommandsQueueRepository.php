<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Repository\CommandsQueue;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Entity\CommandsQueueEntity;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;
use DateTimeImmutable;
use PDO;
use Throwable;

readonly class CommandsQueueRepository implements CommandsQueueRepositoryInterface
{
    private const MESSAGE = '[CommandsQueueRepository] Failed get last insert id';

    public function __construct(
        private GetDatabaseConnectionInterface $getDatabaseConnection
    ) {
    }

    public function save(SaveCommandsQueueDto $saveCommandsQueueDto): CommandId
    {
        try {
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

            $lastInsertId = $connection->lastInsertId();

            if ($lastInsertId === false) {
                throw new CommandsQueueRepositoryException(self::MESSAGE);
            }

            return new CommandId((int)$lastInsertId);
        } catch (Throwable $throwable) {
            throw new CommandsQueueRepositoryException($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }

    /**
     * @inheritDoc
     */
    public function bulkSave(iterable $commands): bool
    {
        try {
            $connection = $this->getDatabaseConnection->handle();

            $placeholders = '';
            $params = [];

            foreach ($commands as $saveCommandsQueueDto) {
                $placeholders .= '(?, ?, ?, ?),';
                $params[] = $saveCommandsQueueDto->getCommand();
                $params[] = $saveCommandsQueueDto->getCommandPid();
                $params[] = $saveCommandsQueueDto->getParentCommandId()?->getValue();
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
        } catch (Throwable $throwable) {
            throw new CommandsQueueRepositoryException($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }

    public function concatCommandIdToColumnByParentCommandId(CommandId $parentCommandId): bool
    {
        try {
            $connection = $this->getDatabaseConnection->handle();

            $statement = $connection->prepare(
                'UPDATE commands_queue
            SET `command` = CONCAT(`command`, " --command_id=", `id`)
            WHERE parent_command_id = ?'
            );

            return $statement->execute([$parentCommandId->getValue()]);
        } catch (Throwable $throwable) {
            throw new CommandsQueueRepositoryException($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }

    public function updateStatusByParentCommandId(
        CommandId $parentCommandId,
        CommandsExecutionLogStatusEnum $status
    ): bool {
        try {
            $connection = $this->getDatabaseConnection->handle();

            $statement = $connection->prepare(
                'UPDATE commands_queue
            SET `status` = ?
            WHERE parent_command_id = ?'
            );

            return $statement->execute([$status->value, $parentCommandId->getValue()]);
        } catch (Throwable $throwable) {
            throw new CommandsQueueRepositoryException($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }

    public function updateStatusByCommandId(
        CommandId $commandId,
        CommandsExecutionLogStatusEnum $status
    ): bool {
        try {
            $connection = $this->getDatabaseConnection->handle();

            $statement = $connection->prepare(
                'UPDATE commands_queue
            SET `status` = ?
            WHERE id = ?'
            );

            return $statement->execute([$status->value, $commandId->getValue()]);
        } catch (Throwable $throwable) {
            throw new CommandsQueueRepositoryException($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }

    /**
     * @inheritDoc
     */
    public function getByParentCommandIdAndStatus(
        CommandId $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        int $limit,
        bool $forUpdate = false
    ): iterable {
        try {
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
                $parentCommandId->getValue(),
                $status->value
            ]);

            while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
                yield new CommandsQueueEntity(
                    new CommandId((int)$row->id),
                    (string)$row->command,
                    $row->command_pid === null ? null : (int)$row->command_pid,
                    $parentCommandId,
                    $status,
                    new DateTimeImmutable((string)$row->created_at)
                );
            }
        } catch (Throwable $throwable) {
            throw new CommandsQueueRepositoryException($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }

    public function updateStatusAndCommandPidByCommandId(
        CommandId $commandId,
        CommandsExecutionLogStatusEnum $status,
        ?int $commandPid,
    ): bool {
        try {
            $connection = $this->getDatabaseConnection->handle();

            $statement = $connection->prepare(
                'UPDATE commands_queue
            SET `status` = ?
            , command_pid = ?
            WHERE id = ?'
            );

            return $statement->execute([$status->value, $commandPid, $commandId->getValue()]);
        } catch (Throwable $throwable) {
            throw new CommandsQueueRepositoryException($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }

    public function findByParentCommandIdAndStatus(
        CommandId $parentCommandId,
        CommandsExecutionLogStatusEnum $status,
        bool $forUpdate = false
    ): ?CommandsQueueEntity {
        try {
            $commands = $this->getByParentCommandIdAndStatus($parentCommandId, $status, 1, $forUpdate);

            foreach ($commands as $command) {
                return $command;
            }

            return null;
        } catch (Throwable $throwable) {
            throw new CommandsQueueRepositoryException($throwable->getMessage(), (int)$throwable->getCode(), $throwable);
        }
    }
}
