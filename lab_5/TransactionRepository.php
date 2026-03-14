<?php

declare(strict_types=1);

class TransactionRepository implements TransactionStorageInterface {
  /** @var Transaction[] */
  private array $transactions = [];

  public function addTransaction(Transaction $transaction): void {
    $this->transactions[] = $transaction;
  }

  public function removeTransactionById(int $id): void {
    $this->transactions = array_values(
      array_filter(
        $this->transactions,
        static fn(Transaction $t): bool => 
          $id !== $t->getId()
      )
    );
  }

  public function getAllTransactions(): array {
    return $this->transactions;
  }

  public function findById(int $id): ?Transaction {
    return array_find(
      $this->transactions,
      static fn(Transaction $t): bool => 
        $id === $t->getId()
    );
  }
}
