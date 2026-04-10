<?php
declare(strict_types = 1);

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