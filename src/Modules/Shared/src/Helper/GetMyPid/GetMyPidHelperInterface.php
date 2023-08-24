<?php

declare(strict_types=1);

namespace App\Modules\Shared\Helper\GetMyPid;

use App\Modules\Shared\Helper\GetMyPid\Exception\GetMyPidHelperException;

interface GetMyPidHelperInterface
{
    /**
     * @throws GetMyPidHelperException
     */
    public function get(): int;
}
