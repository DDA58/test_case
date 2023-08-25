<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Service\SaveCommandsQueue;

use App\Modules\CommandsQueue\Dto\SaveCommandsQueueDto;
use App\Modules\CommandsQueue\Service\SaveCommandsQueue\Exception\SaveCommandsQueueServiceException;
use App\Modules\Shared\ValueObject\CommandId;

interface SaveCommandsQueueServiceInterface
{
    /**
     * @throws SaveCommandsQueueServiceException
     */
    public function handle(SaveCommandsQueueDto $saveCommandsQueueDto): CommandId;
}
