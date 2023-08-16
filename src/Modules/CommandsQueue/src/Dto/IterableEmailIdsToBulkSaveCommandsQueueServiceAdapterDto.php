<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Dto;

use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;

readonly class IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto
{
    public function __construct(
        private iterable $emails,
        private string $commandTemplate,
        private ?int $parentCommandId,
        private int $emailsPerCommand,
        private ?int $commandPid,
        private CommandsExecutionLogStatusEnum $status,
    ) {
    }

    public function getEmails(): iterable
    {
        return $this->emails;
    }

    public function getCommandTemplate(): string
    {
        return $this->commandTemplate;
    }

    public function getParentCommandId(): ?int
    {
        return $this->parentCommandId;
    }

    public function getEmailsPerCommand(): int
    {
        return $this->emailsPerCommand;
    }

    public function getCommandPid(): ?int
    {
        return $this->commandPid;
    }

    public function getStatus(): CommandsExecutionLogStatusEnum
    {
        return $this->status;
    }
}