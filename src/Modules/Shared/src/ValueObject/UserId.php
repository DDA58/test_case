<?php

declare(strict_types=1);

namespace App\Modules\Shared\ValueObject;

use App\Modules\Shared\Exception\InvalidArgumentException;

readonly class UserId
{
    private const MESSAGE = '[UserId] Invalid';

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        private string $id
    ) {
        if (preg_match('/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/', $this->id) !== 1) {
            throw new InvalidArgumentException(self::MESSAGE);
        }
    }
}
