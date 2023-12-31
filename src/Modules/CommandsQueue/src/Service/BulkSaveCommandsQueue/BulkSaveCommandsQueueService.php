<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\BulkSaveCommandsQueue;

use App\Core\Database\Transaction\TransactionInterface;
use App\Modules\CommandsQueue\Repository\CommandsQueue\CommandsQueueRepositoryInterface;
use App\Modules\CommandsQueue\Repository\CommandsQueue\Exception\CommandsQueueRepositoryException;
use App\Modules\CommandsQueue\Service\BulkSaveCommandsQueue\Exception\BulkSaveCommandsQueueServiceException;
use Throwable;

readonly class BulkSaveCommandsQueueService implements BulkSaveCommandsQueueServiceInterface
{
    public function __construct(
        private CommandsQueueRepositoryInterface $commandsQueueRepository,
        private TransactionInterface $transaction,
        private int $amountRowsInPackage,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function handle(iterable $commands): void
    {
        $this->transaction->begin();

        try {
            $buffer = [];

            foreach ($commands as $command) {
                $buffer[] = $command;

                if (count($buffer) === $this->amountRowsInPackage) {
                    $this->commandsQueueRepository->bulkSave($buffer);

                    $buffer = [];
                }
            }

            if ($buffer !== []) {
                $this->commandsQueueRepository->bulkSave($buffer);
            }

            $this->transaction->commit();
        } catch (CommandsQueueRepositoryException $exception) {
            $this->transaction->rollback();

            throw new BulkSaveCommandsQueueServiceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
