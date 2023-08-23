<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Entity;

use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use DateTimeImmutable;

readonly class CommandsQueueEntity
{
    public function __construct(
        private int $id,
        private string $command,
        private ?int $commandPid,
        private ?int $parentCommandId,
        private CommandsExecutionLogStatusEnum $status,
        private DateTimeImmutable $created
    ) {
    }

    public function getId(): int
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

    public function getParentCommandId(): ?int
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
