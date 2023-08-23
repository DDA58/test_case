<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\SaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Service\SaveCommandsQueue\Exception\SaveCommandsQueueServiceException;

interface SaveCommandsQueueServiceInterface
{
    /**
     * @throws SaveCommandsQueueServiceException
     */
    public function handle(SaveCommandsQueueDto $saveCommandsQueueDto): int;
}
