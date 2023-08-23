<?php

declare(strict_types=1);

namespace App\Modules\Notify\Command;

use App\Modules\Notify\Enum\EmailTypeEnum;
use App\Modules\Notify\UseCase\SendEmail\SendEmailUseCaseInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'notify:after_subscription_expired')]
class NotifyAfterSubscriptionExpiredCommand extends Command
{
    private const EMAIL_IDS_OPTION = 'email_ids';
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
            ->addOption(self::EMAIL_IDS_OPTION, null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandId = (int)$input->getOption(self::COMMAND_ID_OPTION);
        $emailIds = array_map(
            static fn(string $emailId): int => (int)$emailId,
            array_filter(
                explode(',', (string)$input->getOption(self::EMAIL_IDS_OPTION))
            )
        );

        if ($emailIds === [] || $commandId === 0) {
            return Command::INVALID;
        }

        $this->sendEmailUseCase->handle(
            $commandId,
            $emailIds,
            EmailTypeEnum::AfterExpireSubscription
        );

        return Command::SUCCESS;
    }
}
