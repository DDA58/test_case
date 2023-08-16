<?php

declare(strict_types=1);

namespace App\Core\Database\Transaction;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use Throwable;

readonly class Transaction implements TransactionInterface
{
    public function __construct(
        private GetDatabaseConnectionInterface $getDatabaseConnection
    ) {
    }

    public function begin(): TransactionInterface
    {
        $this->getDatabaseConnection->handle()->beginTransaction();

        return $this;
    }

    public function commit(): TransactionInterface
    {
        $this->getDatabaseConnection->handle()->commit();

        return $this;
    }

    public function rollback(): TransactionInterface
    {
        $this->getDatabaseConnection->handle()->rollBack();

        return $this;
    }

    public function transaction(callable $callback): mixed
    {
        try {
            $this->begin();

            $result = $callback();

            $this->commit();
        } catch (Throwable $throwable) {
            $this->rollback();

            throw $throwable;
        }

        return $result;
    }

    public function __destruct()
    {
        $this->getDatabaseConnection->handle()->inTransaction() && $this->rollback();
    }
}