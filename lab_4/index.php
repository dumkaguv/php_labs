<?php

declare(strict_types = 1);

/**
 * Массив транзакций (каждая транзакция — ассоциативный массив).
 * @var array<int, array{id:int, date:string, amount:float, description:string, merchant:string}>
 */
$transactions = [
    [
        "id" => 1,
        "date" => "2019-01-01",
        "amount" => 100.00,
        "description" => "Payment for groceries",
        "merchant" => "SuperMart",
    ],
    [
        "id" => 2,
        "date" => "2020-02-15",
        "amount" => 75.50,
        "description" => "Dinner with friends",
        "merchant" => "Local Restaurant",
    ],
    [
        "id" => 3,
        "date" => "2021-07-10",
        "amount" => 250.00,
        "description" => "Phone purchase",
        "merchant" => "ElectroShop",
    ],
];

/**
 * Вычисляет общую сумму всех транзакций.
 *
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @return float
 */
function calculateTotalAmount(array $transactions): float {
  return array_reduce(
    $transactions, 
  static fn(float $total, array $transaction): float => 
              $total + $transaction['amount'],
        0.0
    );
}

/**
 * Ищет транзакции по части описания (регистронезависимо).
 *
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @param string $descriptionPart
 * @return array<int, array{id:int, date:string, amount:float, description:string, merchant:string}>
 */
function findTransactionByDescription(array $transactions, string $descriptionPart): array
{
    $needle = strtolower(trim($descriptionPart));
    if ($needle === '') {
        return [];
    }

    return array_values(array_filter(
        $transactions,
        static fn(array $t): bool =>
            stripos(strtolower($t['description']), $needle) !== false
    ));
}

/**
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @return array{id:int, date:string, amount:float, description:string, merchant:string}|null
 */
function findTransactionById(array $transactions, int $id): array {
  return array_find(
    $transactions,
    static fn(array $t): bool => $t['id'] === $id
  );
}

/**
 * Возвращает количество дней между датой транзакции и текущим днем.
 *
 * @param string $date Дата транзакции в формате YYYY-MM-DD
 * @return int
 */
function daysSinceTransaction(string $date): int
{
    $transactionDate = new DateTime($date);
    $today = new DateTime('today');
    $diff = $transactionDate->diff($today);

    return (int)$diff->days;
}

/**
 * Добавляет новую транзакцию (использует глобальную переменную $transactions).
 *
 * @param int $id
 * @param string $date
 * @param float $amount
 * @param string $description
 * @param string $merchant
 * @return void
 */
function addTransaction(
    array &$transactions,
    int $id,
    string $date,
    float $amount,
    string $description,
    string $merchant
): void {
    $transactions[] = [
        "id" => $id,
        "date" => $date,
        "amount" => $amount,
        "description" => $description,
        "merchant" => $merchant,
    ];
}

/**
 * Удаляет транзакцию по ID (если найдена).
 *
 * @param int $id
 * @return bool true если удалили, иначе false
 */
/**
 * Удаляет транзакцию по ID (если найдена).
 *
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> &$transactions
 * @param int $id
 * @return bool true если удалили, иначе false
 */
function deleteTransactionById(array &$transactions, int $id): bool
{
    $originalCount = count($transactions);

    $transactions = array_values(array_filter(
        $transactions,
        static fn(array $t): bool => $t['id'] !== $id
    ));

    return count($transactions) < $originalCount;
}

/**
 * Сортирует транзакции по дате (по возрастанию).
 *
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @return array<int, array{id:int, date:string, amount:float, description:string, merchant:string}>
 */
function sortTransactionsByDate(array $transactions): array
{
    usort(
        $transactions,
        function (array $a, array $b): int {
            $da = new DateTime($a['date']);
            $db = new DateTime($b['date']);
            return $da <=> $db;
        }
    );

    return $transactions;
}

/**
 * Сортирует транзакции по сумме (по убыванию).
 *
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @return array<int, array{id:int, date:string, amount:float, description:string, merchant:string}>
 */
function sortTransactionsByAmountDesc(array $transactions): array
{
    usort(
        $transactions,
        fn(array $a, array $b): int => $b['amount'] <=> $a['amount']
    );

    return $transactions;
}

/**
 * Возвращает следующий уникальный ID (на основе текущих транзакций).
 *
 * @param array<int, array{id:int, date:string, amount:float, description:string, merchant:string}> $transactions
 * @return int
 */
function nextTransactionId(array $transactions): int
{
    $max = 0;

    foreach ($transactions as $t) {
        if ($t['id'] > $max) {
            $max = $t['id'];
        }
    }

    return $max + 1;
}

// Добавим новую транзакцию (пример)
addTransaction(
    $transactions,
    nextTransactionId($transactions),
    date('Y-m-d'),
    49.99,
    'Online subscription',
    'StreamService'
);

// Удалим транзакцию по ID (пример: удалим id=2, если есть)
deleteTransactionById($transactions, 2);

// Поиск по описанию (пример)
$searchQuery = $_GET['search'] ?? '';
$foundByDescription = $searchQuery !== ''
    ? findTransactionByDescription($transactions, $searchQuery)
    : [];

// Сортировка (выбираем режим из query string: ?sort=date или ?sort=amount)
$sortMode = $_GET['sort'] ?? 'date';
$sortedTransactions = ($sortMode === 'amount')
    ? sortTransactionsByAmountDesc($transactions)
    : sortTransactionsByDate($transactions);

$totalAmount = calculateTotalAmount($sortedTransactions);


?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>ЛР №4 — Транзакции и Галерея</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        header, footer { padding: 12px; background: #f2f2f2; border-radius: 8px; }
        nav a { margin-right: 12px; }
        .card { margin-top: 16px; padding: 12px; border: 1px solid #ddd; border-radius: 8px; }

        table { border-collapse: collapse; width: 100%; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        form { margin-bottom: 12px; }
        input, select { padding: 6px; }
        button { padding: 6px 10px; }
        .total { margin-top: 12px; font-weight: bold; }

        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 10px; margin-top: 12px; }
        .gallery img { width: 100%; height: 140px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd; }
        .muted { color: #666; }
    </style>
</head>
<body>

<header>
    <h1>Лабораторная работа №4</h1>
    <p class="muted">Задание 1: массивы/функции. Задание 2: файловая система (галерея).</p>
</header>

<nav style="margin-top: 12px;">
    <a href="?sort=date">Сортировка по дате</a>
    <a href="?sort=amount">Сортировка по сумме (убыв.)</a>
</nav>

<section class="card">
    <h2>Задание 1 — Транзакции</h2>

    <form method="get">
        <input type="text" name="search" placeholder="Поиск по описанию..."
               value="<?= htmlspecialchars($searchQuery) ?>">
        <select name="sort">
            <option value="date" <?= $sortMode === 'date' ? 'selected' : '' ?>>
                Сортировать по дате
            </option>
            <option value="amount" <?= $sortMode === 'amount' ? 'selected' : '' ?>>
                Сортировать по сумме (убыв.)
            </option>
        </select>
        <button type="submit">Применить</button>
    </form>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Дата</th>
            <th>Сумма</th>
            <th>Описание</th>
            <th>Получатель</th>
            <th>Дней прошло</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($sortedTransactions as $t): ?>
            <tr>
                <td><?= (int)$t['id'] ?></td>
                <td><?= htmlspecialchars($t['date']) ?></td>
                <td><?= number_format($t['amount'], 2, '.', ' ') ?></td>
                <td><?= htmlspecialchars($t['description']) ?></td>
                <td><?= htmlspecialchars($t['merchant']) ?></td>
                <td><?= daysSinceTransaction($t['date']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">
        Общая сумма: <?= number_format($totalAmount, 2, '.', ' ') ?>
    </div>

    <?php if ($searchQuery !== ''): ?>
        <h3>Результаты поиска по описанию:</h3>
        <?php if (empty($foundByDescription)): ?>
            <p class="muted">Ничего не найдено.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($foundByDescription as $t): ?>
                    <li>
                        #<?= (int)$t['id'] ?> —
                        <?= htmlspecialchars($t['description']) ?> —
                        <?= number_format($t['amount'], 2, '.', ' ') ?> —
                        <?= htmlspecialchars($t['merchant']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</section>

<section class="card">
    <h2>Задание 2 — Галерея изображений (image/)</h2>

    <div class="gallery">
        <?php
        $dir = 'image/';
        $files = @scandir($dir);

        if ($files === false) {
            echo "<p class='muted'>Не удалось прочитать директорию image/</p>";
        } else {
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $path = $dir . $file;
                if (!is_file($path)) {
                    continue;
                }

                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg'], true)) {
                    continue;
                }
                ?>
                <img src="<?= htmlspecialchars($path) ?>" alt="<?= htmlspecialchars($file) ?>">
                <?php
            }
        }
        ?>
    </div>
</section>
</body>
</html>