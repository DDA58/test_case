<?php

declare(strict_types=1);

namespace App\Modules\Shared\Helper\Exec;

interface ExecHelperInterface
{
    public function exec(string $command): false|string;
}
