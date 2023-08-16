<?php

declare(strict_types=1);

namespace App\Modules\Shared\Helper\ProcessCreator;

use Symfony\Component\Process\Process;

readonly class ProcessCreatorHelper implements ProcessCreatorHelperInterface
{
    public function __construct(
        private string $appPath,
    ) {
    }

    public function create(string $command): Process
    {
        return Process::fromShellCommandline($command, $this->appPath);
    }
}