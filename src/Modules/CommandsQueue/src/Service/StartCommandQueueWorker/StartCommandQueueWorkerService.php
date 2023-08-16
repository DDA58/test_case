<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\StartCommandQueueWorker;

use App\Modules\CommandsQueue\Command\SimpleByProcessesNotifyWorkerCommand;
use App\Modules\Shared\Helper\Exec\ExecHelperInterface;

readonly class StartCommandQueueWorkerService implements StartCommandQueueWorkerServiceInterface
{
    public function __construct(
        private ExecHelperInterface $execHelper,
        private string $appPath,
        private string $phpBinaryPath,
    ) {
    }

    public function handle(int $parentCommandId): void
    {
        $this->execHelper->exec(
            sprintf(
                '%s %s/bin/console %s --parent_command_id=%d > /dev/null 2>&1 &',
                $this->phpBinaryPath,
                $this->appPath,
                SimpleByProcessesNotifyWorkerCommand::getDefaultName(),
                $parentCommandId
            )
        );
    }
}