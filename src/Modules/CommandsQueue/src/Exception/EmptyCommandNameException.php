<?php

declare(strict_types=1);

namespace App\Modules\CommandsQueue\Exception;

use App\Modules\Shared\Exception\DomainException;

class EmptyCommandNameException extends DomainException
{
}