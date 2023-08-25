<?php

declare(strict_types=1);

namespace App\Modules\Notify\Command;

use App\Modules\Notify\Enum\EmailTypeEnum;
use App\Modules\Notify\UseCase\SendEmail\SendEmailUseCaseInterface;
use App\Modules\Shared\Exception\InvalidArgumentException;
use App\Modules\Shared\ValueObject\CommandId;
use App\Modules\Shared\ValueObject\EmailId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'notify:after_subscription_expired')]
class NotifyAfterSubscriptionExpiredCommand extends Command
{
    private const EMAIL_ID_OPTION = 'email_id';
    private const COMMAND_ID_OPTION = 'command_id';

    public function __construct(
        private readonly SendEmailUseCaseInterface $sendEmailUseCase,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption(self::COMMAND_ID_OPTION, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::EMAIL_ID_OPTION, null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $emailId = new EmailId(
                (int)$input->getOption(self::EMAIL_ID_OPTION)
            );
            $commandId = new CommandId(
                (int)$input->getOption(self::COMMAND_ID_OPTION)
            );
        } catch (InvalidArgumentException) {
            return Command::INVALID;
        }

        $this->sendEmailUseCase->handle(
            $commandId,
            $emailId,
            EmailTypeEnum::AfterExpireSubscription
        );

        return Command::SUCCESS;
    }
}
