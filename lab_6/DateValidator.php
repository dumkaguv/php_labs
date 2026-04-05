<?php

declare(strict_types=1);

/**
 * Validates a date field in Y-m-d format.
 */
final class DateValidator implements ValidatorInterface
{
    /**
     * Configures whether future dates are allowed.
     */
    public function __construct(
        private bool $allowFutureDates = false
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function validate(string $label, mixed $value): array
    {
        if (!is_string($value) || $value === '') {
            return ["Поле \"$label\" содержит некорректную дату."];
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();

        $hasDateErrors = is_array($errors)
            && (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0);

        if ($date === false || $hasDateErrors || $date->format('Y-m-d') !== $value) {
            return ["Поле \"$label\" должно быть датой в формате ГГГГ-ММ-ДД."];
        }

        if (!$this->allowFutureDates && $date > new DateTimeImmutable('today')) {
            return ["Поле \"$label\" не может содержать будущую дату."];
        }

        return [];
    }
}
