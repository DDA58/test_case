<?php

declare(strict_types=1);

namespace App\Modules\Shared\ValueObject;

use App\Modules\Shared\Exception\InvalidArgumentException;

readonly class CommandId
{
    private const MESSAGE = '[CommandId] Invalid';

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        private int $commandId
    ) {
        if ($this->commandId <= 0) {
            throw new InvalidArgumentException(self::MESSAGE);
        }
    }

    public function getValue(): int
    {
        return $this->commandId;
    }
}
