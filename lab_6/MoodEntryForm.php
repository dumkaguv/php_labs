<?php

declare(strict_types=1);

/**
 * Normalizes and validates incoming form data.
 */
final class MoodEntryForm
{
    /**
     * @var array<string, string|string[]>
     */
    private array $values;

    /**
     * @var array<string, string[]>
     */
    private array $errors = [];

    /**
     * @var array<string, mixed>
     */
    private array $validatedData = [];

    /**
     * Creates a form object from raw request data.
     *
     * @param array<string, mixed> $source
     */
    public function __construct(array $source = [])
    {
        $this->values = $this->normalizeValues($source);
    }

    /**
     * Runs validation rules for every supported field.
     */
    public function validate(): bool
    {
        $this->errors = [];
        $this->validatedData = [];

        foreach ($this->getRules() as $field => $validators) {
            $value = $this->values[$field];
            $label = $this->getFieldLabel($field);

            foreach ($validators as $validator) {
                $messages = $validator->validate($label, $value);

                if ($messages !== []) {
                    $this->errors[$field] = $messages;
                    break;
                }
            }
        }

        if ($this->errors !== []) {
            return false;
        }

        $this->validatedData = [
            'title' => $this->values['title'],
            'author' => $this->values['author'],
            'entry_date' => $this->values['entry_date'],
            'mood' => $this->values['mood'],
            'energy_level' => $this->values['energy_level'],
            'sleep_hours' => (float) $this->values['sleep_hours'],
            'activities' => $this->values['activities'],
            'gratitude' => $this->values['gratitude'],
            'notes' => $this->values['notes'],
        ];

        return true;
    }

    /**
     * Returns normalized values for form repopulation.
     *
     * @return array<string, string|string[]>
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Returns validation errors grouped by field name.
     *
     * @return array<string, string[]>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Returns the first validation error for a field, if any.
     */
    public function getFirstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Returns validated typed data ready for entry creation.
     *
     * @return array<string, mixed>
     */
    public function getValidatedData(): array
    {
        return $this->validatedData;
    }

    /**
     * Normalizes raw request values into a fixed internal structure.
     *
     * @param array<string, mixed> $source
     * @return array<string, string|string[]>
     */
    private function normalizeValues(array $source): array
    {
        return [
            'title' => $this->normalizeText($source['title'] ?? ''),
            'author' => $this->normalizeText($source['author'] ?? ''),
            'entry_date' => $this->normalizeScalar($source['entry_date'] ?? ''),
            'mood' => $this->normalizeScalar($source['mood'] ?? ''),
            'energy_level' => $this->normalizeScalar($source['energy_level'] ?? ''),
            'sleep_hours' => $this->normalizeScalar($source['sleep_hours'] ?? ''),
            'activities' => $this->normalizeActivities($source['activities'] ?? []),
            'gratitude' => $this->normalizeTextarea($source['gratitude'] ?? ''),
            'notes' => $this->normalizeTextarea($source['notes'] ?? ''),
        ];
    }

    /**
     * Normalizes a scalar value into a trimmed string.
     */
    private function normalizeScalar(mixed $value): string
    {
        return is_scalar($value)
            ? trim((string) $value)
            : '';
    }

    /**
     * Normalizes a short text field and compresses repeated whitespace.
     */
    private function normalizeText(mixed $value): string
    {
        $text = $this->normalizeScalar($value);
        $normalized = preg_replace('/\s+/u', ' ', $text);

        return $normalized ?? $text;
    }

    /**
     * Normalizes multiline text values while keeping line breaks readable.
     */
    private function normalizeTextarea(mixed $value): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        return trim(str_replace(["\r\n", "\r"], "\n", (string) $value));
    }

    /**
     * Normalizes the checkbox group into a unique list of selected options.
     *
     * @return string[]
     */
    private function normalizeActivities(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $activities = [];

        foreach ($value as $item) {
            if (is_scalar($item)) {
                $normalizedItem = trim((string) $item);

                if ($normalizedItem !== '') {
                    $activities[] = $normalizedItem;
                }
            }
        }

        return array_values(array_unique($activities));
    }

    /**
     * Builds the validation rules for every field.
     *
     * @return array<string, ValidatorInterface[]>
     */
    private function getRules(): array
    {
        return [
            'title' => [
                new RequiredValidator(),
                new StringLengthValidator(3, 80),
            ],
            'author' => [
                new RequiredValidator(),
                new StringLengthValidator(2, 40),
            ],
            'entry_date' => [
                new RequiredValidator(),
                new DateValidator(),
            ],
            'mood' => [
                new RequiredValidator(),
                new ChoiceValidator(array_keys(MoodEntryOptions::MOODS)),
            ],
            'energy_level' => [
                new RequiredValidator(),
                new ChoiceValidator(array_keys(MoodEntryOptions::ENERGY_LEVELS)),
            ],
            'sleep_hours' => [
                new RequiredValidator(),
                new NumberRangeValidator(0, 24),
            ],
            'activities' => [
                new ArrayChoiceValidator(array_keys(MoodEntryOptions::ACTIVITIES), 1, 4),
            ],
            'gratitude' => [
                new RequiredValidator(),
                new StringLengthValidator(10, 250),
            ],
            'notes' => [
                new RequiredValidator(),
                new StringLengthValidator(15, 600),
            ],
        ];
    }

    /**
     * Returns the human-readable field label used in validation messages.
     */
    private function getFieldLabel(string $field): string
    {
        return match ($field) {
            'title' => 'Заголовок',
            'author' => 'Автор',
            'entry_date' => 'Дата записи',
            'mood' => 'Настроение',
            'energy_level' => 'Уровень энергии',
            'sleep_hours' => 'Часы сна',
            'activities' => 'Активности',
            'gratitude' => 'Благодарность',
            'notes' => 'Заметки',
            default => $field,
        };
    }
}
