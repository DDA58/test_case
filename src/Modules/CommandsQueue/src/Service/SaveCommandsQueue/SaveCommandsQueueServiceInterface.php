<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\SaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;

interface SaveCommandsQueueServiceInterface
{
    public function handle(SaveCommandsQueueDto $saveCommandsQueueDto): int;
}