<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Command;

use App\Core\Database\Transaction\TransactionInterface;
use App\Modules\CommandsQueue\Dto\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto;
use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Service\ConcatCommandIdToColumnByParentCommandId\ConcatCommandIdToColumnByParentCommandIdServiceInterface;
use App\Modules\CommandsQueue\Service\IterableEmailIdsToBulkSaveCommandsQueue\IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterInterface;
use App\Modules\CommandsQueue\Service\SaveCommandsQueue\SaveCommandsQueueServiceInterface;
use App\Modules\CommandsQueue\Service\StartCommandQueueWorker\StartCommandQueueWorkerServiceInterface;
use App\Modules\CommandsQueue\Service\UpdateStatusByCommandId\UpdateStatusByCommandIdServiceInterface;
use App\Modules\CommandsQueue\Service\UpdateStatusByParentCommandId\UpdateStatusByParentCommandIdServiceInterface;
use App\Modules\Email\Service\GetEmailsWithPreExpirationSubscription\GetEmailsWithPreExpirationSubscriptionServiceInterface;
use App\Modules\Notify\Command\NotifyBeforeSubscriptionExpirationCommand;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use PDOException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'fill_commands_queue:before_expiration')]
class FillCommandsQueueBeforeExpirationCommand extends Command
{
    private const DAYS_BEFORE_EXPIRATION = 'days_before_expiration';
    private const EMAILS_PER_COMMAND = 'emails_per_command';

    public function __construct(
        private readonly GetEmailsWithPreExpirationSubscriptionServiceInterface $getEmailsWithPreExpirationSubscriptionService,
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
            ->addOption(self::DAYS_BEFORE_EXPIRATION, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::EMAILS_PER_COMMAND, null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $daysBeforeExpiration = (int)$input->getOption(self::DAYS_BEFORE_EXPIRATION);
        $emailsPerCommand = (int)$input->getOption(self::EMAILS_PER_COMMAND);

        if ($daysBeforeExpiration <= 0 || $emailsPerCommand <= 0) {
            return Command::INVALID;
        }

        $id = $this->saveCommandsQueueService->handle(new SaveCommandsQueueDto(
            implode(' ', ['php', ...$_SERVER['argv']]),
            getmypid(),
            null,
            CommandsExecutionLogStatusEnum::Started
        ));

        $emailIds = $this->getEmailsWithPreExpirationSubscriptionService->handle($daysBeforeExpiration);
        $commandName = NotifyBeforeSubscriptionExpirationCommand::getDefaultName();

        $this->emailIdsToBulkSaveCommandsQueueServiceAdapter->handle(new IterableEmailIdsToBulkSaveCommandsQueueServiceAdapterDto(
            $emailIds,
            sprintf('%s %s/bin/console %s --days_before_expiration=%d --email_ids=', $this->phpBinaryPath, $this->appPath, $commandName, $daysBeforeExpiration),
            $id,
            $emailsPerCommand,
            null,
            CommandsExecutionLogStatusEnum::Creating
        ));

        try {
            $status = CommandsExecutionLogStatusEnum::Failed;

            $this->transaction->begin();

            $this->concatCommandIdToColumnByParentCommandIdService->handle($id);
            $this->updateStatusByParentCommandIdService->handle($id, CommandsExecutionLogStatusEnum::Created);

            $this->transaction->commit();

            $status = CommandsExecutionLogStatusEnum::Success;
        } catch (PDOException $exception) {
            $this->transaction->rollBack();

            return Command::FAILURE;
        } finally {
            $this->updateStatusByCommandIdService->handle($id, $status);
        }

        $this->startCommandQueueWorkerService->handle($id);

        return Command::SUCCESS;
    }
}
