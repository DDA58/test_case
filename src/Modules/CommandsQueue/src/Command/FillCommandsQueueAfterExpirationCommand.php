<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Command;

use App\Core\Database\Transaction\TransactionInterface;
use App\Modules\CommandsQueue\Dto\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto;
use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Exception\EmptyCommandNameException;
use App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId\ConcatCommandIdToColumnByParentCommandIdServiceInterface;
use App\Modules\CommandsQueue\Service\IterableEmailIdsToBulkSaveCommandsQueue\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterInterface;
use App\Modules\CommandsQueue\Service\SaveCommandsQueue\SaveCommandsQueueServiceInterface;
use App\Modules\CommandsQueue\Service\StartCommandQueueWorker\StartCommandQueueWorkerServiceInterface;
use App\Modules\CommandsQueue\Service\UpdateStatusByCommandId\UpdateStatusByCommandIdServiceInterface;
use App\Modules\CommandsQueue\Service\UpdateStatusByParentCommandId\UpdateStatusByParentCommandIdServiceInterface;
use App\Modules\Email\Service\GetEmailsWithExpiredSubscription\GetEmailsWithExpiredSubscriptionServiceInterface;
use App\Modules\Notify\Command\NotifyAfterSubscriptionExpiredCommand;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(name: 'fill_commands_queue:after_expiration')]
class FillCommandsQueueAfterExpirationCommand extends Command
{
    private const EMAILS_PER_COMMAND = 'emails_per_command';

    public function __construct(
        private readonly GetEmailsWithExpiredSubscriptionServiceInterface $getEmailsWithExpiredSubscriptionService,
        private readonly SaveCommandsQueueServiceInterface $saveCommandsQueueService,
        private readonly IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterInterface $emailIdsToBulkSaveCommandsQueueServiceAdapter,
        private readonly ConcatCommandIdToColumnByParentCommandIdServiceInterface $concatCommandIdToColumnByParentCommandIdService,
        private readonly UpdateStatusByParentCommandIdServiceInterface $updateStatusByParentCommandIdService,
        private readonly UpdateStatusByCommandIdServiceInterface $updateStatusByCommandIdService,
        private readonly TransactionInterface $transaction,
        private readonly StartCommandQueueWorkerServiceInterface $startCommandQueueWorkerService,
        private readonly string $appPath,
        private readonly string $phpBinaryPath,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption(self::EMAILS_PER_COMMAND, null, InputOption::VALUE_REQUIRED);
    }

    /**
     * @throws Throwable
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $emailsPerCommand = (int)$input->getOption(self::EMAILS_PER_COMMAND);

        if ($emailsPerCommand <= 0) {
            return Command::INVALID;
        }

        $id = null;
        $status = CommandsExecutionLogStatusEnum::Failed;

        try {
            $this->transaction->begin();

            $id = $this->saveCommandsQueueService->handle(new SaveCommandsQueueDto(
                implode(' ', ['php', ...$_SERVER['argv'] ?? []]),
                getmypid(),
                null,
                CommandsExecutionLogStatusEnum::Started
            ));

            $emailIds = $this->getEmailsWithExpiredSubscriptionService->handle();
            $commandName = NotifyAfterSubscriptionExpiredCommand::getDefaultName();

            if ($commandName === null) {
                $this->transaction->commit();

                //TODO log
                throw new EmptyCommandNameException('[FillCommandsQueueAfterExpirationCommand] Empty command name');
            }

            $this->emailIdsToBulkSaveCommandsQueueServiceAdapter->handle(new IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto(
                $emailIds,
                sprintf('%s %s/bin/console %s --email_ids=', $this->phpBinaryPath, $this->appPath, $commandName),
                $id,
                $emailsPerCommand,
                null,
                CommandsExecutionLogStatusEnum::Creating
            ));

            $this->concatCommandIdToColumnByParentCommandIdService->handle($id);
            $this->updateStatusByParentCommandIdService->handle($id, CommandsExecutionLogStatusEnum::Created);

            $this->transaction->commit();

            $status = CommandsExecutionLogStatusEnum::Success;
        } catch (Throwable $throwable) {
            $this->transaction->rollBack();

            throw $throwable;
        } finally {
            try {
                $id !== null && $this->updateStatusByCommandIdService->handle($id, $status);
            } finally {
                if ($id === null) {
                    //TODO log
                }
            }
        }

        $this->startCommandQueueWorkerService->handle($id);

        return Command::SUCCESS;
    }
}
