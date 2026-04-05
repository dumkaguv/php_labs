<?php

declare(strict_types=1);

/**
 * Describes a reusable validator for a form field.
 */
interface ValidatorInterface
{
    /**
     * Validates a single field value.
     *
     * @return string[] Validation error messages. Empty array means success.
     */
    public function validate(string $label, mixed $value): array;
}
