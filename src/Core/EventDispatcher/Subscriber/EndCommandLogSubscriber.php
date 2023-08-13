<?php

declare(strict_types=1);

namespace App\Core\EventDispatcher\Subscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class EndCommandLogSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private float $startExecutionTime
    ) {
    }

    public function handle(ConsoleTerminateEvent $event): void
    {
        $event->getOutput()->writeln(
            sprintf(
                'End command "%s". PID: %s. Time: %f',
                $event->getCommand()->getName(),
                getmypid(),
                microtime(true) - $this->startExecutionTime
            )
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::TERMINATE => 'handle',
        ];
    }
}