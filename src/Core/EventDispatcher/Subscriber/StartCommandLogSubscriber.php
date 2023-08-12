<?php

declare(strict_types=1);

namespace App\Core\EventDispatcher\Subscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StartCommandLogSubscriber implements EventSubscriberInterface
{
    public function handle(ConsoleCommandEvent $event): void
    {
        $event->getOutput()->writeln(
            sprintf('Start command "%s". PID: %s', $event->getCommand()->getName(), getmypid())
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'handle',
        ];
    }
}