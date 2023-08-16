<?php

declare(strict_types=1);

namespace App\Modules\Shared\Enum;

enum CommandsExecutionLogStatusEnum: string
{
    case Creating = 'creating';
    case Created = 'created';
    case Started = 'started';
    case Success = 'success';
    case Failed = 'failed';
}
