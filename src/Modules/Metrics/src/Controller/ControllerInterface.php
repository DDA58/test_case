<?php

declare(strict_types=1);

namespace App\Modules\Metrics\Controller;

use App\Modules\Metrics\Request\Request;
use App\Modules\Metrics\Response\Response;

interface ControllerInterface
{
    public function __invoke(Request $request): Response;
}
