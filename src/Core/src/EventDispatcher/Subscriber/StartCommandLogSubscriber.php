<?php

declare(strict_types=1);

namespace App\Core\EventDispatcher\Subscriber;

use App\Modules\Shared\Helper\GetMyPid\GetMyPidHelperInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class StartCommandLogSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private GetMyPidHelperInterface $getMyPidHelper,
        private float $startExecutionTime,
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
            sprintf(
                'Start command "%s". PID: %s. Start time: %s',
                $command->getName(),
                $this->getMyPidHelper->get(),
                date(DateTimeInterface::ATOM, (int)$this->startExecutionTime)
            )
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'handle',
        ];
    }
}
