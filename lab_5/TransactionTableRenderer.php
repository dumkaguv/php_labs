<?php

declare(strict_types = 1);

final class TransactionTableRenderer {
  final public function render(array $transactions): string {
    $tableHeaderHTML = $this->getTableHeaderHTML();
    $tableBodyHTML = $this->getTableBodyHTML($transactions);

    return "
      $tableHeaderHTML
      $tableBodyHTML
      </table>
    ";
  }

  private function getTableHeaderHTML(): string {
    return "
      <table class='transactions-table'>
        <thead>
          <tr>
            <th>ID</th>
            <th>Дата</th>
            <th>Сумма</th>
            <th>Описание</th>
            <th>Получатель</th>
            <th>Дней с момента транзакции</th>
          </tr>
        </thead>
    ";
  }

  private function getTableBodyHTML(array $transactions): string {
    $rows = '';

    foreach ($transactions as $transaction) {
      $formattedDate = $transaction->getDate()->format('Y-m-d');
      $formattedAmount = number_format($transaction->getAmount(), 2, '.', ' ');

      $rows .= "
        <tr>
          <td class='col-id'>{$transaction->getId()}</td>
          <td>{$formattedDate}</td>
          <td class='col-amount'>{$formattedAmount}</td>
          <td>{$transaction->getDescription()}</td>
          <td>{$transaction->getMerchant()}</td>
          <td class='col-days'>{$transaction->getDaysSinceTransaction()}</td>
        </tr>
      ";
    }

    return "
      <tbody>
        $rows
      </tbody>
    ";
  }
}
