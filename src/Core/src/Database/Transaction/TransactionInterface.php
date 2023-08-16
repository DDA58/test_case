<?php

declare(strict_types=1);

namespace App\Core\Database\Transaction;

use Throwable;

interface TransactionInterface
{
    /**
     * Начинает транзакцию.
     */
    public function begin(): TransactionInterface;

    /**
     * Подтверждает транзакцию.
     */
    public function commit(): TransactionInterface;

    /**
     * Откатывает транзакцию.
     */
    public function rollback(): TransactionInterface;

    /**
     * Оборачивает в транзакцию выполнение пользовательского кода.
     *
     * @throws Throwable
     */
    public function transaction(callable $callback): mixed;
}
