<?php

declare(strict_types=1);

namespace App\Modules\Shared\Helper\USleep;

interface USleepHelperInterface
{
    /**
     * @param int<0, max> $microseconds
     */
    public function sleep(int $microseconds): void;
}
