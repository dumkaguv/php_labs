<?php

declare(strict_types=1);

/**
 * Validates that a scalar value is one of the allowed options.
 */
final class ChoiceValidator implements ValidatorInterface
{
    /**
     * @param string[] $allowedValues
     */
    public function __construct(
        private array $allowedValues
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function validate(string $label, mixed $value): array
    {
        if (!is_string($value) || !in_array($value, $this->allowedValues, true)) {
            return ["Поле \"$label\" содержит недопустимое значение."];
        }

        return [];
    }
}
