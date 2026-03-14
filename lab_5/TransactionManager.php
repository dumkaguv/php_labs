<?php

declare(strict_types=1);

class TransactionManager {
  public function __construct(
    private TransactionStorageInterface $repository
  ) {}

  public function calculateTotalAmount(?array $transactions = null): float {
    $arrayToUse = $transactions ?? $this->repository->getAllTransactions();

    return array_reduce(
      $arrayToUse,
      static fn(float $total, Transaction $t): float => 
        $total + $t->getAmount(),
      0.0
    );
  }

  public function calculateTotalAmountByDateRange(
    string $startDate, string $endDate
  ): float {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);

    return $this->calculateTotalAmount(
      array_filter(
        $this->repository->getAllTransactions(),
        static fn(Transaction $t): bool =>
          $t->getDate() >= $start && $t->getDate() <= $end
      )
    );
  }

  public function countTransactionsByMerchant(string $merchant): int {
    return count(
      array_filter(
        $this->repository->getAllTransactions(),
        static fn(Transaction $t): bool => 
          $t->getMerchant() === $merchant
      )
    );
  }

  public function sortTransactionsByDate(): array {
    $transactions = $this->getTransactionCopy();

    usort(
      $transactions,
      static fn(Transaction $a, Transaction $b) =>
        $a->getDate() <=> $b->getDate()
    );

    return $transactions;
  }

  public function sortTransactionsByAmountDesc(): array {
    $transactions = $this->getTransactionCopy();

    usort(
      $transactions,
      static fn(Transaction $a, Transaction $b) =>
        $b->getAmount() <=> $a->getAmount()
    );

    return $transactions;
  }

  private function getTransactionCopy(): array {
    $transactions = $this->repository->getAllTransactions();

    return $transactions;
  }
}
