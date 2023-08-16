<?php

declare(strict_types=1);

namespace App\Modules\Shared\Helper\USleep;

interface USleepHelperInterface
{
    public function sleep(int $microseconds): void;
}