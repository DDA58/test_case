<?php

declare(strict_types=1);

namespace App\Enum;

enum CommandsExecutionLogStatusEnum: string
{
    case Created = 'created';
    case Started = 'started';
    case Success = 'success';
    case Failed = 'failed';
}
