<?php

declare(strict_types=1);

namespace App\Metrics\Controller;

use App\Metrics\Request\Request;
use App\Metrics\Response\Response;

interface ControllerInterface
{
    public function __invoke(Request $request): Response;
}
