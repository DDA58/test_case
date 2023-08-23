<?php

declare(strict_types=1);

namespace App\Modules\Metrics\Service\IsCronRunning;

readonly class IsCronRunningService
{
    public function __invoke(): bool
    {
        /** @psalm-suppress ForbiddenCode */
        $commandResult = shell_exec('/etc/init.d/cron status > /dev/null; echo $?');

        return (int)$commandResult === 0;
    }
}
