<?php

declare(strict_types=1);

namespace App\Metrics\Service\IsCronRunning;

class IsCronRunningService
{
    public function __invoke(): bool
    {
        $commandResult = shell_exec('/etc/init.d/cron status > /dev/null; echo $?');

        return (int)$commandResult === 0;
    }
}