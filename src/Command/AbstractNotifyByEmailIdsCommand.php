<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use App\Enum\EmailSendLogTypeEnum;
use PDO;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractNotifyByEmailIdsCommand extends Command
{
    private const EMAIL_IDS_OPTION = 'email_ids';
    private const COMMAND_ID_OPTION = 'command_id';

    abstract protected function getEmailSendLogType(): EmailSendLogTypeEnum;

    abstract protected function getFrom(): string;

    protected function configure(): void
    {
        $this
            ->addOption(self::COMMAND_ID_OPTION, null, InputOption::VALUE_REQUIRED)
            ->addOption(self::EMAIL_IDS_OPTION, null, InputOption::VALUE_REQUIRED)
            ->addOption('command_id', null, InputOption::VALUE_REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandId = (int)$input->getOption(self::COMMAND_ID_OPTION);
        $emailIds = explode(',', $input->getOption(self::EMAIL_IDS_OPTION));

        if ($emailIds === [] || $commandId === 0) {
            return Command::INVALID;
        }

        $connection = GetDatabaseConnection::getInstance();

        foreach ($emailIds as $emailId) {
            $connection->beginTransaction();
            $statement = $connection->prepare(
                'SELECT users.`uuid` AS `user_uuid`
, users.username
, emails.id AS `email_id`
, emails.email
, emails.confirmed AS `email_confirmed`
, emails.`checked` AS `email_checked`
, emails.valid AS `email_valid`
FROM emails
JOIN users ON emails.user_uuid = users.uuid AND emails.is_last = 1
WHERE emails.id = ?
FOR UPDATE'
            );
            $statement->execute([
                $emailId
            ]);
            $email = $statement->fetch(PDO::FETCH_OBJ);

            if ($email === false) {
                $connection->commit();

                continue;
            }

            if (
                $email->email_confirmed === 1
                || $email->email_valid === 1
            ) {
                $connection->commit();

                $this->sendEmail($email, $this->generateText($email->username), $email->email_checked, $email->email_valid, $commandId);

                continue;
            }

            if ($email->email_checked === 1 && $email->email_valid === 0) {
                $connection->commit();

                continue;
            }

            $result = check_email($email->email);

            $statement = $connection->prepare(
                'UPDATE emails
        SET `checked` = 1,
            `valid` = ?
        WHERE id = ?'
            );
            $statement->execute([
                $result,
                $emailId
            ]);

            $connection->commit();

            if ($result === 1) {
                $this->sendEmail($email, $this->generateText($email->username), 1, $result, $commandId);
            }
        }

        return Command::SUCCESS;
    }

    protected function getEmailTemplate(): string
    {
        return '%s, your subscription is expiring soon';
    }

    private function generateText(string $username): string
    {
        return sprintf($this->getEmailTemplate(), $username);
    }

    private function sendEmail(object $email, string $text, int $checked, int $valid, int $commandId): void
    {
        send_email($this->getFrom(), $email->email, $text);

        $connection = GetDatabaseConnection::getInstance();
        $statement = $connection->prepare(
            'INSERT INTO emails_send_log(`type`, command_id, email_id, confirmed, checked, valid) VALUES(?, ?, ?, ?, ?, ?);'
        );
        $statement->execute([
            $this->getEmailSendLogType()->value,
            $commandId,
            $email->email_id,
            $email->email_confirmed,
            $checked,
            $valid
        ]);
    }
}
