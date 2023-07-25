<?php

declare(strict_types=1);

namespace App\Command;

interface CommandInterface
{
    public static function getName(): string;

    public function handle(array $arguments = []): int;
}
