<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\IterableEmailIdsToBulkSaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto;
use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Service\BulkSaveCommandsQueue\BulkSaveCommandsQueueServiceInterface;
use App\Modules\CommandsQueue\Service\BulkSaveCommandsQueue\Exception\BulkSaveCommandsQueueServiceException;
use App\Modules\CommandsQueue\Service\IterableEmailIdsToBulkSaveCommandsQueue\Exception\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterException;

readonly class IterableEmailIdsToBulkSaveCommandsQueueServiceAdapter implements
    IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterInterface
{
    public function __construct(
        private BulkSaveCommandsQueueServiceInterface $bulkSaveCommandsQueueService
    ) {
    }

    public function handle(IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto $dto): void
    {
        try {
            $this->bulkSaveCommandsQueueService->handle(
                $this->makeIterableCommands($dto)
            );
        } catch (BulkSaveCommandsQueueServiceException $exception) {
            throw new IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @return iterable<int, SaveCommandsQueueDto>
     */
    private function makeIterableCommands(IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto $dto): iterable
    {
        $commandTemplate = $dto->getCommandTemplate();
        $parentCommandId = $dto->getParentCommandId();
        $commandPid = $dto->getCommandPid();
        $commandStatus = $dto->getStatus();
        $emailIdsInCommand = [];

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
