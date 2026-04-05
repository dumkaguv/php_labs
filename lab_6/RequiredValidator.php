<?php

declare(strict_types=1);

/**
 * Validates that a value is not empty.
 */
final class RequiredValidator implements ValidatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function validate(string $label, mixed $value): array
    {
        if ($value === null) {
            return ["Поле \"$label\" обязательно для заполнения."];
        }

        if (is_array($value)) {
            return $value === []
                ? ["Поле \"$label\" обязательно для заполнения."]
                : [];
        }

        if (is_string($value) && trim($value) === '') {
            return ["Поле \"$label\" обязательно для заполнения."];
        }

        return [];
    }
}
