<?php

declare(strict_types = 1);

function getMockTransactions(): array {
  return [
    new Transaction(1, '2024-01-02', 120.50, 'Покупка кофе', 'Starbucks'),
    new Transaction(2, '2024-01-05', 560.00, 'Покупка продуктов', 'Lidl'),
    new Transaction(3, '2024-01-10', 89.99, 'Подписка на музыку', 'Spotify'),
    new Transaction(4, '2024-01-15', 1200.00, 'Оплата аренды', 'Landlord'),
    new Transaction(5, '2024-01-20', 45.30, 'Поездка на такси', 'Uber'),
    new Transaction(6, '2024-02-01', 250.00, 'Покупка одежды', 'Zara'),
    new Transaction(7, '2024-02-05', 19.99, 'Мобильная связь', 'Vodafone'),
    new Transaction(8, '2024-02-10', 300.00, 'Покупка электроники', 'Amazon'),
    new Transaction(9, '2024-02-15', 75.00, 'Ужин в ресторане', 'Pizza Hut'),
    new Transaction(10, '2024-02-20', 150.00, 'Подарок другу', 'Gift Shop'),
  ];
} 
