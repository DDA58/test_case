<?php

declare(strict_types=1);

namespace App\Enum;

enum DefaultExitCodesEnum: int
{
    case Success = 0;
    case Fail = 1;
    case Invalid = 2;
}
