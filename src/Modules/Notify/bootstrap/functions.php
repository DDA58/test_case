<?php

declare(strict_types=1);

function check_email(string $email): int
{
    sleep(rand(1, 60));
//    sleep(rand(1, 1)); // For faster test

    return rand(0, 1);
}

function send_email(string $from, string $to, string $text): void
{
    sleep(rand(1, 10));
//    sleep(rand(1, 1)); // For faster test
}

function exception_log_and_notify(Throwable $throwable): void
{
    var_dump(
        $throwable->getMessage(),
        $throwable->getFile()
    );

    //TODO Try Kibana, sentry, tg, etc

    exit(1);
}

function error_log_and_notify(int $type, string $message, string $file, int $line): void
{
    $exception = new ErrorException($message, 0, $type, $file, $line);

    exception_log_and_notify($exception);
}
