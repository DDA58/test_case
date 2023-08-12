<?php

declare(strict_types=1);

namespace App\Core\EventDispatcher\Subscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ErrorCommandLogSubscriber implements EventSubscriberInterface
{
    public function handle(ConsoleErrorEvent $event): void
    {
        exception_log_and_notify($event->getError());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::ERROR => 'handle',
        ];
    }
}