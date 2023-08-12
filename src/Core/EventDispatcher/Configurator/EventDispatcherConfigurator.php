<?php

declare(strict_types=1);

namespace App\Core\EventDispatcher\Configurator;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcherConfigurator
{
    public function __construct(
        private readonly float $startExecutionTime
    ) {
    }

    public function __invoke(EventDispatcherInterface $dispatcher): void
    {
        $dispatcher->addListener(
            ConsoleEvents::COMMAND,
            static fn(ConsoleCommandEvent $event) => $event->getOutput()->writeln(
                sprintf('Start command "%s". PID: %s', $event->getCommand()->getName(), getmypid())
            )
        );
        $dispatcher->addListener(
            ConsoleEvents::TERMINATE,
            fn(ConsoleTerminateEvent $event) => $event->getOutput()->writeln(
                sprintf(
                    'End command "%s". PID: %s. Time: %f',
                    $event->getCommand()->getName(),
                    getmypid(),
                    microtime(true) - $this->startExecutionTime
                )
            )
        );
        $dispatcher->addListener(
            ConsoleEvents::ERROR,
            static fn(ConsoleErrorEvent $event) => exception_log_and_notify($event->getError())
        );
    }
}
