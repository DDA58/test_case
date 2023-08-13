<?php

declare(strict_types=1);

namespace App\Metrics\Controller;

use App\Metrics\Request\Request;
use App\Metrics\Response\Response;
use App\Metrics\Service\IsCronRunning\IsCronRunningService;
use OpenMetricsPhp\Exposition\Text\Collections\GaugeCollection;
use OpenMetricsPhp\Exposition\Text\HttpResponse;
use OpenMetricsPhp\Exposition\Text\Metrics\Gauge;
use OpenMetricsPhp\Exposition\Text\Types\MetricName;

readonly class MetricsController implements ControllerInterface
{
    public function __invoke(Request $request): Response
    {
        $isCronRunning = (new IsCronRunningService())();

        $counters = GaugeCollection::fromGauges(
            MetricName::fromString('is_cron_running'),
            Gauge::fromValue((float)$isCronRunning),
        )->withHelp('Is Cron running in container?');

        $vendorResponse = HttpResponse::fromMetricCollections($counters);

        return new Response(
            (string)$vendorResponse->getBody(),
            $vendorResponse->getStatusCode(),
            [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Content-Encoding' => 'UTF-8',
                'Connection' => 'close',
            ]
        );
    }
}
