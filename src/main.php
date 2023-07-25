<?php

declare(strict_types=1);

use App\Command\CommandInterface;
use App\Command\InitAfterExpirationCommand;
use App\Command\InitBeforeExpirationCommand;
use App\Command\InitDatabaseCommand;
use App\Command\NotifyAboutOneDayBeforeSubscriptionExpirationCommand;
use App\Command\NotifyAboutThreeDaysBeforeSubscriptionExpirationCommand;
use App\Command\NotifyAfterSubscriptionExpiredCommand;
use App\Command\SimpleByDatabaseNotifyWorkerCommand;
use App\Command\SimpleByProcessesNotifyWorkerCommand;
use App\Enum\DefaultExitCodesEnum;

ini_set('memory_limit', '256M');

require dirname(__DIR__) . '/vendor/autoload.php';

define('APP_PATH', dirname(__DIR__));

$commandName = $argv[1] ?? '';

if ($commandName === '') {
    echo 'Add command name when call this script' . PHP_EOL;

    exit(DefaultExitCodesEnum::Invalid->value);
}

set_exception_handler('exception_log_and_notify');

set_error_handler('error_log_and_notify');

$runCommandWithLog = static function (CommandInterface $command, array $arguments): void {
    $start = microtime(true);
    $pid = getmypid();
    $commandName = $command::getName();

    echo sprintf('Start command "%s". PID: %s', $commandName, $pid) . PHP_EOL;

    $exitCode = $command->handle($arguments);

    echo sprintf('End command "%s". PID: %s. Time: %f', $commandName, $pid, microtime(true) - $start) . PHP_EOL;

    exit($exitCode);
};

switch ($commandName) {
    case InitDatabaseCommand::getName():
        $runCommandWithLog(new InitDatabaseCommand(), $argv);

        break;

    case InitBeforeExpirationCommand::getName():
        $runCommandWithLog(new InitBeforeExpirationCommand(), $argv);

        break;

    case InitAfterExpirationCommand::getName():
        $runCommandWithLog(new InitAfterExpirationCommand(), $argv);

        break;

    case NotifyAboutOneDayBeforeSubscriptionExpirationCommand::getName():
        $runCommandWithLog(new NotifyAboutOneDayBeforeSubscriptionExpirationCommand(), $argv);

        break;

    case NotifyAboutThreeDaysBeforeSubscriptionExpirationCommand::getName():
        $runCommandWithLog(new NotifyAboutThreeDaysBeforeSubscriptionExpirationCommand(), $argv);

        break;

    case NotifyAfterSubscriptionExpiredCommand::getName():
        $runCommandWithLog(new NotifyAfterSubscriptionExpiredCommand(), $argv);

        break;

    case SimpleByDatabaseNotifyWorkerCommand::getName():
        $runCommandWithLog(new SimpleByDatabaseNotifyWorkerCommand(), $argv);

        break;

    case SimpleByProcessesNotifyWorkerCommand::getName():
        $runCommandWithLog(new SimpleByProcessesNotifyWorkerCommand(), $argv);

        break;

    default:
        echo sprintf('Command "%s" not found', $commandName) . PHP_EOL;

        exit(DefaultExitCodesEnum::Invalid->value);
}
