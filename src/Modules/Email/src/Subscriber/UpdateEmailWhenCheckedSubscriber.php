<?php

declare(strict_types=1);

namespace App\Modules\Email\Subscriber;

use App\Modules\Email\Service\UpdateCheckedAndValidById\UpdateCheckedAndValidByIdServiceInterface;
use App\Modules\Notify\Event\EmailCheckedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class UpdateEmailWhenCheckedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UpdateCheckedAndValidByIdServiceInterface $updateCheckedAndValidByIdService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EmailCheckedEvent::NAME => 'handle',
        ];
    }

    public function handle(EmailCheckedEvent $event): void
    {
        $this->updateCheckedAndValidByIdService->handle($event->getEmailId(), true, $event->isValid());
    }
}