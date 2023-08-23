<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Dto;

use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

readonly class SaveCommandsQueueDto
{
    public function __construct(
        private string $command,
        private ?int $commandPid,
        private ?int $parentCommandId,
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

    public function getParentCommandId(): ?int
    {
        return $this->parentCommandId;
    }

    public function getStatus(): CommandsExecutionLogStatusEnum
    {
        return $this->status;
    }
}
