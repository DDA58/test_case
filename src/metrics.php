<?php

declare(strict_types=1);

use App\Metrics\Controller\HealthCheckController;
use App\Metrics\Controller\MetricsController;
use App\Metrics\Request\Request;
use App\Metrics\Response\Response;

require dirname(__DIR__) . '/vendor/autoload.php';

date_default_timezone_set('Europe/Moscow');

$socket = socket_create_listen(8181);

$address = '127.0.0.1';
$port = 8181;
socket_getsockname($socket, $address, $port);

print sprintf('Server Listening on %s:%s', $address, $port) . PHP_EOL;

while ($currentSocketConnection = socket_accept($socket)) {
    $request = Request::createFromString(
        (string)socket_read($currentSocketConnection, 128)
    );

    print ($request->getMethod() ?? 'Undefined') . '---' . ($request->getPath() ?? 'Undefined') . PHP_EOL;

    $response = null;

    if ($request->getMethod() === 'GET') {
        switch ($request->getPath()) {
            case '/metrics':
                $response = (new MetricsController())($request);

                break;
            case '/healthcheck':
                $response = (new HealthCheckController())($request);

                break;
        }
    }

    $response ??= new Response('', Response::HTTP_NOT_FOUND);

    $message = (string)$response;

    $bytes = socket_send($currentSocketConnection, $message, strlen($message), 0);

    print $bytes === false
        ? 'Error when socket_send. Code: ' . socket_last_error($currentSocketConnection)
        : sprintf('Bytes send %d', $bytes) . PHP_EOL;

    socket_close($currentSocketConnection);
}

print 'Server stopped';

socket_close($socket);
