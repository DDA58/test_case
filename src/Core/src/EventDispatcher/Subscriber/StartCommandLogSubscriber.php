<?php

declare(strict_types=1);

namespace App\Core\EventDispatcher\Subscriber;

use App\Modules\Shared\Helper\GetMyPid\GetMyPidHelperInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class StartCommandLogSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private GetMyPidHelperInterface $getMyPidHelper
    ) {
    }

    public function handle(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        if ($command === null) {
            return;
        }

        /** @psalm-suppress PossiblyNullArgument */
        $event->getOutput()->writeln(
            sprintf('Start command "%s". PID: %s', $command->getName(), $this->getMyPidHelper->get())
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'handle',
        ];
    }
}
