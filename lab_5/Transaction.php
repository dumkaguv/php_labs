<?php

declare(strict_types=1);

class Transaction {
    private int $id;
    private DateTime $date;
    private float $amount;
    private string $description;
    private string $merchant;

    public function __construct(
        int $id,
        string $date,
        float $amount,
        string $description,
        string $merchant
    ) {
        $this->id = $id;
        $this->date = new DateTime($date);
        $this->amount = $amount;
        $this->description = $description;
        $this->merchant = $merchant;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getAmount(): float {
        return $this->amount;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getMerchant(): string {
        return $this->merchant;
    }

    public function getDaysSinceTransaction(): int {
        $today = new DateTime();
        $interval = $this->date->diff($today);

        return $interval->days;
    }
}
