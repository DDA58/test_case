<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\SendEmail;

use App\Modules\Notify\Dto\EmailForNotifyDto;
use App\Modules\Notify\Enum\EmailTypeEnum;
use App\Modules\Notify\Event\EmailSentSuccessfulEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class SendEmailService implements SendEmailServiceInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private string $emailSenderFrom,
    ) {
    }

    public function handle(
        EmailTypeEnum $type,
        EmailForNotifyDto $email,
        string $text,
        int $commandId,
    ): void {
        send_email($this->emailSenderFrom, $email->getEmail(), $text);

        $this->dispatcher->dispatch(
            new EmailSentSuccessfulEvent(
                $type,
                $commandId,
                $email->getEmailId(),
                $email->isEmailConfirmed(),
                $email->isEmailChecked(),
                $email->isEmailValid()
            ),
            EmailSentSuccessfulEvent::NAME
        );
    }
}
