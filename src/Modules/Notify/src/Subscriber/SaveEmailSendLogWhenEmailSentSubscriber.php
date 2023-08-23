<?php

declare(strict_types=1);

namespace App\Modules\Notify\Subscriber;

use App\Modules\Notify\Dto\SaveEmailsSendLogDto;
use App\Modules\Notify\Event\EmailSentSuccessfulEvent;
use App\Modules\Notify\Service\SaveEmailsSendLog\Exception\SaveEmailsSendLogServiceException;
use App\Modules\Notify\Service\SaveEmailsSendLog\SaveEmailsSendLogServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class SaveEmailSendLogWhenEmailSentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SaveEmailsSendLogServiceInterface $saveEmailsSendLogService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EmailSentSuccessfulEvent::NAME => 'handle',
        ];
    }

    public function handle(EmailSentSuccessfulEvent $event): void
    {
        try {
            $this->saveEmailsSendLogService->handle(new SaveEmailsSendLogDto(
                $event->getType(),
                $event->getCommandId(),
                $event->getEmailId(),
                $event->isEmailConfirmed(),
                $event->isEmailChecked(),
                $event->isEmailValid(),
            ));
        } catch (SaveEmailsSendLogServiceException) {
            //TODO log
        }
    }
}
