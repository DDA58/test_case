<?php

declare(strict_types=1);

namespace App\Modules\Notify\Command;

use App\Modules\Notify\Service\BeforeSubscriptionExpirationEmailTypeDetector\BeforeSubscriptionExpirationEmailTypeDetectorServiceInterface;
use App\Modules\Notify\UseCase\SendEmail\SendEmailUseCaseInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'notify:before_subscription_expiration')]
class NotifyBeforeSubscriptionExpirationCommand extends Command
{
    private const DAYS_BEFORE_EXPIRATION = 'days_before_expiration';
    private const EMAIL_IDS_OPTION = 'email_ids';
    private const COMMAND_ID_OPTION = 'command_id';

    public function __construct(
        private readonly SendEmailUseCaseInterface $sendEmailUseCase,
        private readonly BeforeSubscriptionExpirationEmailTypeDetectorServiceInterface $beforeSubscriptionExpirationEmailTypeDetectorService,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption(self::COMMAND_ID_OPTION, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::EMAIL_IDS_OPTION, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::DAYS_BEFORE_EXPIRATION, null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandId = (int)$input->getOption(self::COMMAND_ID_OPTION);
        $emailIds = explode(',', $input->getOption(self::EMAIL_IDS_OPTION));
        $daysBeforeExpiration = (int)$input->getOption(self::DAYS_BEFORE_EXPIRATION);

        if ($emailIds === [] || $commandId === 0 || $daysBeforeExpiration <= 0) {
            return Command::INVALID;
        }

        $emailType = $this->beforeSubscriptionExpirationEmailTypeDetectorService->handle($daysBeforeExpiration);

        $this->sendEmailUseCase->handle(
            $commandId,
            $emailIds,
            $emailType
        );

        return Command::SUCCESS;
    }
}
