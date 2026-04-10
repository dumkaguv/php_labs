<?php
declare(strict_types = 1);

require_once __DIR__ . '/functions.php';

$transactions = require __DIR__ . '/transactions.php';

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
