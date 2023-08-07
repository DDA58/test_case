<?php

declare(strict_types=1);

namespace App\Metrics\Controller;

use App\Metrics\Request\Request;
use App\Metrics\Response\Response;
use App\Metrics\Service\IsCronRunning\IsCronRunningService;

class HealthCheckController implements ControllerInterface
{
    public function __invoke(Request $request): Response
    {
        return new Response(
            '',
            (new IsCronRunningService())() ? Response::HTTP_OK : Response::HTTP_INTERNAL_SERVER_ERROR,
            [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Content-Encoding' => 'UTF-8',
                'Connection' => 'close',
            ]
        );
    }
}