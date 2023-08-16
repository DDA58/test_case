<?php

declare(strict_types=1);

namespace App\Modules\Shared\Helper\Exec;

readonly class ExecHelper implements ExecHelperInterface
{
    public function exec(string $command): false|string
    {
        return exec($command);
    }
}