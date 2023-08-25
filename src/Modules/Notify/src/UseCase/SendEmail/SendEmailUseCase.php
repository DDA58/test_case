<?php

declare(strict_types=1);

namespace App\Modules\Notify\UseCase\SendEmail;

use App\Core\Database\Transaction\TransactionInterface;
use App\Modules\Notify\Dto\EmailForNotifyDto;
use App\Modules\Notify\Enum\EmailTypeEnum;
use App\Modules\Notify\Service\EmailChecker\EmailCheckerServiceInterface;
use App\Modules\Notify\Service\FindNotifyEmail\Exception\FindNotifyEmailServiceException;
use App\Modules\Notify\Service\FindNotifyEmail\FindNotifyEmailServiceInterface;
use App\Modules\Notify\Service\RenderEmail\RenderEmailServiceInterface;
use App\Modules\Notify\Service\SendEmail\SendEmailServiceInterface;
use App\Modules\Shared\ValueObject\Email;
use App\Modules\Shared\ValueObject\EmailId;

readonly class SendEmailUseCase implements SendEmailUseCaseInterface
{
    public function __construct(
        private FindNotifyEmailServiceInterface $findNotifyEmailService,
        private EmailCheckerServiceInterface $emailCheckerService,
        private TransactionInterface $transaction,
        private RenderEmailServiceInterface $renderEmailService,
        private SendEmailServiceInterface $sendEmailService,
    ) {
    }

    public function handle(int $commandId, EmailId $emailId, EmailTypeEnum $emailType): void
    {
        $this->transaction->begin();

        try {
            $email = $this->findNotifyEmailService->findByEmailId($emailId, true);
        } catch (FindNotifyEmailServiceException) {
            $email = null;
        }

        if ($email === null) {
            $this->transaction->rollback();

            return;
        }

        if (
            $email->isEmailConfirmed() === true
            || $email->isEmailValid() === true
        ) {
            $this->transaction->commit();

            $this->sendEmailService->handle(
                $emailType,
                $email,
                $this->renderEmailService->handle($emailType, [$email->getUsername()]),
                $commandId
            );

            return;
        }

        if ($email->isEmailChecked() === true && $email->isEmailValid() === false) {
            $this->transaction->commit();

            return;
        }

        $result = $this->emailCheckerService->handle(
            $emailId,
            new Email($email->getEmail()),
        );

        $this->transaction->commit();

        if ($result === true) {
            $this->sendEmailService->handle(
                $emailType,
                new EmailForNotifyDto(
                    $email->getUserUuid(),
                    $email->getUsername(),
                    $email->getEmailId(),
                    $email->getEmail(),
                    $email->isEmailConfirmed(),
                    true,
                    true
                ),
                $this->renderEmailService->handle($emailType, [$email->getUsername()]),
                $commandId
            );
        }
    }
}
