<?php

declare(strict_types=1);

namespace App\Modules\Metrics\Controller;

use App\Modules\Metrics\Request\Request;
use App\Modules\Metrics\Response\Response;
use App\Modules\Metrics\Service\IsCronRunning\IsCronRunningService;

readonly class HealthCheckController implements ControllerInterface
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
