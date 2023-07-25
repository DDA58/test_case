<?php

declare(strict_types=1);

namespace App\Command;

use App\Core\Database\GetDatabaseConnection;
use App\Enum\CommandsExecutionLogStatusEnum;
use App\Enum\DefaultExitCodesEnum;
use App\Enum\EmailSendLogTypeEnum;
use PDO;

abstract class AbstractNotifyBeforeSubscriptionExpirationCommand implements CommandInterface
{
    private const EMAIL_IDS_OPTION = 'email_ids';
    private const PARENT_COMMAND_ID_OPTION = 'parent_command_id';

    abstract protected function getEmailSendLogType(): EmailSendLogTypeEnum;

    abstract protected function getFrom(): string;

    public function handle(array $arguments = []): int
    {
        $emailIds = [];
        $parentCommandId = 0;

        foreach ($arguments as $arg) {
            if (str_starts_with($arg, '--' . self::EMAIL_IDS_OPTION) === true) {
                $emailIds = explode(',', $this->extractOptionValue($arg, self::EMAIL_IDS_OPTION));

                array_walk($emailIds, static fn(string &$emailId): int => $emailId = (int)$emailId);
            } elseif (str_starts_with($arg, '--' . self::PARENT_COMMAND_ID_OPTION) === true) {
                $parentCommandId = (int)$this->extractOptionValue($arg, self::PARENT_COMMAND_ID_OPTION);
            }
        }

        if ($emailIds === [] || $parentCommandId === 0) {
            return DefaultExitCodesEnum::Invalid->value;
        }

        $connection = GetDatabaseConnection::getInstance();
        $connection->beginTransaction();
        $statement = $connection->prepare(
            'SELECT *
            FROM commands_queue
            WHERE parent_command_id = ?
                AND `status` = ?
                AND `command` REGEXP ?
            LIMIT 1
            FOR UPDATE'
        );
        $statement->execute([
            $parentCommandId,
            CommandsExecutionLogStatusEnum::Created->value,
            implode(' ', $arguments) . '$'
        ]);
        $currentCommand = $statement->fetch(PDO::FETCH_OBJ);

        if ($currentCommand === false) {
            $connection->commit();
            //Not found
            return DefaultExitCodesEnum::Invalid->value;
        }

        $statement = $connection->prepare(
            'UPDATE commands_queue
            SET `status` = ?
            , command_pid = ?
            WHERE id = ?'
        );
        $statement->execute([
            CommandsExecutionLogStatusEnum::Started->value,
            getmypid(),
            $currentCommand->id
        ]);
        $connection->commit();

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

                $this->sendEmail($email, $this->generateText($email->username), $email->email_checked, $email->email_valid, $currentCommand->id);

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
                $this->sendEmail($email, $this->generateText($email->username), 1, $result, $currentCommand->id);
            }
        }

        $statement = $connection->prepare(
            'UPDATE commands_queue
            SET `status` = ?
            WHERE id = ?'
        );
        $statement->execute([
            CommandsExecutionLogStatusEnum::Success->value,
            $currentCommand->id
        ]);

        return DefaultExitCodesEnum::Success->value;
    }

    protected function getEmailTemplate(): string
    {
        return '%s, your subscription is expiring soon';
    }

    private function generateText(string $username): string
    {
        return sprintf($this->getEmailTemplate(), $username);
    }

    private function extractOptionValue(string $value, string $optionName): string
    {
        return str_replace('--' . $optionName . '=', '', $value);
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
