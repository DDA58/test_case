<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Command;

use App\Core\Database\Transaction\TransactionInterface;
use App\Modules\CommandsQueue\Service\FindByParentCommandIdAndStatus\FindByParentCommandIdAndStatusServiceInterface;
use App\Modules\CommandsQueue\Service\GetByParentCommandIdAndStatus\GetByParentCommandIdAndStatusServiceInterface;
use App\Modules\CommandsQueue\Service\UpdateStatusAndCommandPidByCommandId\UpdateStatusAndCommandPidByCommandIdServiceInterface;
use App\Modules\CommandsQueue\Service\UpdateStatusByCommandId\UpdateStatusByCommandIdServiceInterface;
use App\Modules\Shared\Enum\CommandsExecutionLogStatusEnum;
use App\Modules\Shared\Helper\ProcessCreator\ProcessCreatorHelperInterface;
use App\Modules\Shared\Helper\USleep\USleepHelperInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TODO Move to supervisor?
 */
#[AsCommand(name: 'notify_worker:by_processes')]
class SimpleByProcessesNotifyWorkerCommand extends Command
{
    private const MAX_THREADS_OPTION = 'max_threads';

    private const PARENT_COMMAND_ID_OPTION = 'parent_command_id';

    public function __construct(
        private readonly GetByParentCommandIdAndStatusServiceInterface $getByParentCommandIdAndStatusService,
        private readonly TransactionInterface $transaction,
        private readonly ProcessCreatorHelperInterface $processCreatorHelper,
        private readonly UpdateStatusAndCommandPidByCommandIdServiceInterface $updateStatusAndCommandPidByCommandIdService,
        private readonly UpdateStatusByCommandIdServiceInterface $updateStatusByCommandIdService,
        private readonly FindByParentCommandIdAndStatusServiceInterface $findByParentCommandIdAndStatusService,
        private readonly USleepHelperInterface $USleepHelper,
        private readonly int $defaultMaxThreads,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption(self::PARENT_COMMAND_ID_OPTION, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::MAX_THREADS_OPTION, null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parentCommandId = (int)$input->getOption(self::PARENT_COMMAND_ID_OPTION);
        $maxThreads = (int)$input->getOption(self::MAX_THREADS_OPTION);

        if ($maxThreads <= 0) {
            $maxThreads = $this->defaultMaxThreads;
        }

        if ($parentCommandId === 0) {
            return Command::INVALID;
        }

        $this->transaction->begin();

        $commands = $this->getByParentCommandIdAndStatusService->handle(
            $parentCommandId,
            CommandsExecutionLogStatusEnum::Created,
            $maxThreads,
            true
        );

        $processes = [];

        /** @var \App\Modules\CommandsQueue\Entity\CommandsQueueEntity $command */
        foreach ($commands as $command) {
            $process = $this->processCreatorHelper->create($command->getCommand());
            $process->setTimeout(0);
            $process->disableOutput();
            $process->start();
            $processes[$command->getId()] = $process;

            $this->updateStatusAndCommandPidByCommandIdService->handle(
                $command->getId(),
                CommandsExecutionLogStatusEnum::Started,
                $process->getPid()
            );
        }

        $this->transaction->commit();

        while (count($processes)) {
            foreach ($processes as $commandId => $runningProcess) {
                if (count($processes) > $maxThreads || $runningProcess->isRunning() === true) {
                    $this->USleepHelper->sleep(10000);

                    continue;
                }

                if ($runningProcess->isRunning() === false) {
                    unset($processes[$commandId]);

                    $this->updateStatusByCommandIdService->handle(
                        $commandId,
                        $runningProcess->getExitCode() ? CommandsExecutionLogStatusEnum::Failed : CommandsExecutionLogStatusEnum::Success
                    );
                }

                $this->transaction->begin();

                $command = $this->findByParentCommandIdAndStatusService->handle(
                    $parentCommandId,
                    CommandsExecutionLogStatusEnum::Created,
                    true
                );

                if ($command === null) {
                    $this->transaction->commit();

                    break;
                }

                $process = $this->processCreatorHelper->create($command->getCommand());
                $process->setTimeout(0);
                $process->disableOutput();
                $process->start();
                $processes[$command->getId()] = $process;

                $this->updateStatusAndCommandPidByCommandIdService->handle(
                    $command->getId(),
                    CommandsExecutionLogStatusEnum::Started,
                    $process->getPid()
                );

                $this->transaction->commit();
            }
        }

        return Command::SUCCESS;
    }
}