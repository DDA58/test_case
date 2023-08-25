<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Dto;

use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;

readonly class SaveCommandsQueueDto
{
    public function __construct(
        private string $command,
        private ?int $commandPid,
        private ?CommandId $parentCommandId,
        private CommandsExecutionLogStatusEnum $status
    ) {
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
}
