<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Dto;

use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\ValueObject\CommandId;

readonly class IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto
{
    /**
     * @param iterable<\App\Modules\Email\Dto\EmailWithExpiredSubscriptionDto|\App\Modules\Email\Dto\EmailWithPreExpirationSubscriptionDto> $emails
     */
    public function __construct(
        private iterable $emails,
        private string $commandTemplate,
        private ?CommandId $parentCommandId,
        private ?int $commandPid,
        private CommandsExecutionLogStatusEnum $status,
    ) {
    }

    /**
     * @return  iterable<\App\Modules\Email\Dto\EmailWithExpiredSubscriptionDto|\App\Modules\Email\Dto\EmailWithPreExpirationSubscriptionDto>
     */
    public function getEmails(): iterable
    {
        return $this->emails;
    }

    public function getCommandTemplate(): string
    {
        return $this->commandTemplate;
    }

    public function getParentCommandId(): ?CommandId
    {
        return $this->parentCommandId;
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
