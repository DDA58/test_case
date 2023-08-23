<?php

declare(strict_types=1);

namespace App\Modules\Shared\Helper\ProcessCreator;

use Symfony\Component\Process\Process;

interface ProcessCreatorHelperInterface
{
    public function create(string $command): Process;
}
