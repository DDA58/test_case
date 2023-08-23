<?php

declare(strict_types=1);

namespace App\Modules\Shared\Helper\USleep;

readonly class USleepHelper implements USleepHelperInterface
{
    /**
     * @inheritDoc
     */
    public function sleep(int $microseconds): void
    {
        usleep($microseconds);
    }
}
