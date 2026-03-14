<?php

declare(strict_types = 1);

require_once __DIR__ . '/Transaction.php';
require_once __DIR__ . '/TransactionStorageInterface.php';
require_once __DIR__ . '/TransactionRepository.php';
require_once __DIR__ . '/TransactionManager.php';
require_once __DIR__ . '/TransactionTableRenderer.php';
require_once __DIR__ . '/getMockTransactions.php';

$repository = new TransactionRepository();
$transactions = getMockTransactions();

foreach ($transactions as $transaction) {
  $repository->addTransaction($transaction);
}

$renderer = new TransactionTableRenderer();
$tableHtml = $renderer->render($transactions);
$totalTransactions = count($transactions);

?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История транзакций</title>
    <style>
      :root {
        --page-bg: linear-gradient(135deg, #f6efe5 0%, #dce9f2 100%);
        --card-bg: rgba(255, 252, 248, 0.88);
        --text-main: #1f2937;
        --text-soft: #5b6472;
        --line: rgba(135, 151, 173, 0.25);
        --accent: #b85c38;
        --accent-soft: rgba(184, 92, 56, 0.12);
        --shadow: 0 24px 60px rgba(41, 53, 72, 0.16);
      }

      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        min-height: 100vh;
        padding: 40px 20px;
        font-family: Candara, "Trebuchet MS", sans-serif;
        color: var(--text-main);
        background: var(--page-bg);
      }

      .table-card {
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.55);
        border-radius: 28px;
        background: var(--card-bg);
        backdrop-filter: blur(14px);
        box-shadow: var(--shadow);
      }

      .table-card__header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
        padding: 24px 28px 18px;
      }

      .table-card__title {
        margin: 0;
        font-size: 24px;
      }

      .table-wrap {
        overflow-x: auto;
        padding: 0 10px 10px;
      }

      .transactions-table {
        width: 100%;
        border-collapse: collapse;
      }

      .transactions-table thead th {
        padding: 16px 18px;
        border-bottom: 1px solid var(--line);
        color: var(--text-soft);
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-align: left;
        text-transform: uppercase;
      }

      .transactions-table tbody tr {
        transition: background-color 0.2s ease;
      }

      .transactions-table tbody tr:nth-child(even) {
        background: rgba(255, 255, 255, 0.4);
      }

      .transactions-table tbody tr:hover {
        background: rgba(184, 92, 56, 0.08);
      }

      .transactions-table tbody td {
        padding: 18px;
        border-bottom: 1px solid var(--line);
        vertical-align: middle;
      }

      .transactions-table tbody tr:last-child td {
        border-bottom: none;
      }

      .col-id,
      .col-days {
        white-space: nowrap;
      }

      .col-amount {
        font-weight: 700;
        color: var(--accent);
      }

      @media (max-width: 720px) {
        body {
          padding: 24px 14px;
        }

        .table-card {
          border-radius: 22px;
        }

        .table-card__header {
          flex-direction: column;
          align-items: flex-start;
          padding: 20px 20px 12px;
        }

        .transactions-table thead th,
        .transactions-table tbody td {
          padding: 14px;
        }

        .hero p {
          font-size: 16px;
        }
      }
    </style>
  </head>
  <body>
      <section class="table-card">
        <div class="table-card__header">
          <h2 class="table-card__title">Список операций (<?= $totalTransactions ?>)</h2>
        </div>

        <div class="table-wrap">
          <?= $tableHtml ?>
        </div>
      </section>
    </main>
  </body>
</html>
