<?php

declare(strict_types=1);

/**
 * Validates that a value is numeric and stays within a given range.
 */
final class NumberRangeValidator implements ValidatorInterface
{
    /**
     * Creates a numeric range validator.
     */
    public function __construct(
        private float $minValue,
        private float $maxValue
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function validate(string $label, mixed $value): array
    {
        if (!is_scalar($value) || !is_numeric((string) $value)) {
            return ["Поле \"$label\" должно быть числом."];
        }

        $number = (float) $value;

        if ($number < $this->minValue || $number > $this->maxValue) {
            return [
                "Поле \"$label\" должно быть в диапазоне от "
                . $this->formatNumber($this->minValue)
                . ' до '
                . $this->formatNumber($this->maxValue)
                . '.',
            ];
        }

        return [];
    }

    /**
     * Formats a numeric boundary for human-readable error output.
     */
    private function formatNumber(float $value): string
    {
        $formatted = number_format($value, 1, '.', '');

        return rtrim(rtrim($formatted, '0'), '.');
    }
}
