<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\IterableEmailIdsToBulkSaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto;
use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Service\BulkSaveCommandsQueue\BulkSaveCommandsQueueServiceInterface;

readonly class IterableEmailIdsToBulkSaveCommandsQueueServiceAdapter implements
    IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterInterface
{
    public function __construct(
        private BulkSaveCommandsQueueServiceInterface $bulkSaveCommandsQueueService
    ) {
    }

    public function handle(IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto $dto): void
    {
        $this->bulkSaveCommandsQueueService->handle(
            $this->makeIterableCommands($dto)
        );
    }

    private function makeIterableCommands(IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto $dto): iterable
    {
        $commandTemplate = $dto->getCommandTemplate();
        $parentCommandId = $dto->getParentCommandId();
        $commandPid = $dto->getCommandPid();
        $commandStatus = $dto->getStatus();
        $emailIdsInCommand = [];

        //TODO Add before_created status
        foreach ($dto->getEmails() as $row) {
            if (count($emailIdsInCommand) === $dto->getEmailsPerCommand()) {
                yield new SaveCommandsQueueDto(
                    $commandTemplate . implode(',', $emailIdsInCommand),
                    $commandPid,
                    $parentCommandId,
                    $commandStatus
                );

                $emailIdsInCommand = [];
            }

            $emailIdsInCommand[] = $row->getEmailId();
        }

        if ($emailIdsInCommand !== []) {
            yield new SaveCommandsQueueDto(
                $commandTemplate . implode(',', $emailIdsInCommand),
                $commandPid,
                $parentCommandId,
                $commandStatus
            );
        }
    }
}