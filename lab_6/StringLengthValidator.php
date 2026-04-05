<?php

declare(strict_types=1);

/**
 * Validates a string value by its length.
 */
final class StringLengthValidator implements ValidatorInterface
{
    /**
     * Creates a new validator with a minimum and maximum length.
     */
    public function __construct(
        private int $minLength,
        private int $maxLength
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function validate(string $label, mixed $value): array
    {
        if (!is_string($value)) {
            return ["Поле \"$label\" содержит некорректное значение."];
        }

        $length = $this->getLength($value);

        if ($length < $this->minLength) {
            return [
                "Поле \"$label\" должно содержать минимум {$this->minLength} символа(ов).",
            ];
        }

        if ($length > $this->maxLength) {
            return [
                "Поле \"$label\" должно содержать не более {$this->maxLength} символов.",
            ];
        }

        return [];
    }

    /**
     * Calculates the length of a string with multibyte support when available.
     */
    private function getLength(string $value): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }
}
