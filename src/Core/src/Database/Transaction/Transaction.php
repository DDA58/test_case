<?php

declare(strict_types=1);

namespace App\Core\Database\Transaction;

use App\Core\Database\Connection\GetDatabaseConnectionInterface;
use PDOException;
use Throwable;

class Transaction implements TransactionInterface
{
    private int $transactionCounter = 0;

    public function __construct(
        private readonly GetDatabaseConnectionInterface $getDatabaseConnection
    ) {
    }

    public function begin(): TransactionInterface
    {
        $this->transactionCounter === 0
            ? $this->getDatabaseConnection->handle()->beginTransaction()
            : $this->getDatabaseConnection->handle()->exec('SAVEPOINT trans' . $this->transactionCounter);

        $this->transactionCounter++;

        return $this;
    }

    public function commit(): TransactionInterface
    {
        $this->transactionCounter--;

        $this->transactionCounter === 0
            ? $this->getDatabaseConnection->handle()->commit()
            : $this->getDatabaseConnection->handle()->exec('RELEASE SAVEPOINT trans' . $this->transactionCounter);

        return $this;
    }

    public function rollback(): TransactionInterface
    {
        if ($this->transactionCounter === 0) {
            throw new PDOException('Rollback error : There is no transaction started');
        }

        $this->transactionCounter--;

        $this->transactionCounter === 0
            ? $this->getDatabaseConnection->handle()->rollBack()
            : $this->getDatabaseConnection->handle()->exec('ROLLBACK TO trans' . ($this->transactionCounter));

        return $this;
    }

    public function transaction(callable $callback): mixed
    {
        try {
            $this->begin();

            /** @var mixed $result */
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
//        $this->transactionCounter > 0 && $this->getDatabaseConnection->handle()->inTransaction() && $this->rollback();
    }
}
