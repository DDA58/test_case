<?php

declare(strict_types=1);

namespace App\Modules\Shared\Helper\GetMyPid;

use App\Modules\Shared\Helper\GetMyPid\Exception\GetMyPidHelperException;

class GetMyPidHelper implements GetMyPidHelperInterface
{
    private const MESSAGE = '[GetMyPidHelper] Failed get pid';

    public function get(): int
    {
        $pid = getmypid();

        if ($pid === false) {
            throw new GetMyPidHelperException(self::MESSAGE);
        }

        return $pid;
    }
}
