<?php

declare(strict_types=1);

namespace App\Modules\Notify\Service\EmailChecker;

use App\Modules\Notify\Event\EmailCheckedEvent;
use App\Modules\Shared\ValueObject\Email;
use App\Modules\Shared\ValueObject\EmailId;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class EmailCheckerService implements EmailCheckerServiceInterface
{
    public function __construct(
        private EventDispatcherInterface $dispatcher
    ) {
    }

    public function handle(EmailId $id, Email $email): bool
    {
        $result = check_email($email->getValue()) === 1;

        $this->dispatcher->dispatch(new EmailCheckedEvent($id, $result), EmailCheckedEvent::NAME);

        return $result;
    }
}
