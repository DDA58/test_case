<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Entity;

use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;
use DateTimeImmutable;

readonly class CommandsQueueEntity
{
    public function __construct(
        private CommandId $id,
        private string $command,
        private ?int $commandPid,
        private ?CommandId $parentCommandId,
        private CommandsExecutionLogStatusEnum $status,
        private DateTimeImmutable $created
    ) {
    }

    public function getId(): CommandId
    {
        return $this->id;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getCommandPid(): ?int
    {
        return $this->commandPid;
    }

    public function getParentCommandId(): ?CommandId
    {
        return $this->parentCommandId;
    }

    public function getStatus(): CommandsExecutionLogStatusEnum
    {
        return $this->status;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }
}
