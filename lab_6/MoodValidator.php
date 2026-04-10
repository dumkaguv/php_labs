<?php

declare(strict_types=1);

/**
 * Validates submitted form data.
 */
final class MoodValidator
{
    /**
     * Validates raw POST data and returns sanitized values.
     *
     * @param array<string, mixed> $data
     * @return array{values: array<string, mixed>, errors: array<string, string>}
     */
    public function validate(array $data): array
    {
        $values = [
            'title' => trim((string) ($data['title'] ?? '')),
            'author' => trim((string) ($data['author'] ?? '')),
            'entry_date' => trim((string) ($data['entry_date'] ?? '')),
            'mood' => trim((string) ($data['mood'] ?? '')),
            'activities' => is_array($data['activities'] ?? null) ? array_values($data['activities']) : [],
            'notes' => trim((string) ($data['notes'] ?? '')),
            'sleep_hours' => trim((string) ($data['sleep_hours'] ?? '')),
        ];

        $errors = [];

        if ($values['title'] === '') {
            $errors['title'] = 'Enter a title.';
        } elseif (strlen($values['title']) < 3) {
            $errors['title'] = 'Title must contain at least 3 characters.';
        }

        if ($values['author'] === '') {
            $errors['author'] = 'Enter an author.';
        }

        if (!$this->isValidDate($values['entry_date'])) {
            $errors['entry_date'] = 'Enter a valid date.';
        }

        if (!array_key_exists($values['mood'], MoodOptions::MOODS)) {
            $errors['mood'] = 'Choose a mood.';
        }

        $validActivities = [];
        foreach ($values['activities'] as $activity) {
            if (is_string($activity) && array_key_exists($activity, MoodOptions::ACTIVITIES)) {
                $validActivities[] = $activity;
            }
        }

        $values['activities'] = array_values(array_unique($validActivities));

        if ($values['activities'] === []) {
            $errors['activities'] = 'Choose at least one activity.';
        }

        if ($values['notes'] === '') {
            $errors['notes'] = 'Enter notes.';
        } elseif (strlen($values['notes']) < 10) {
            $errors['notes'] = 'Notes must contain at least 10 characters.';
        }

        if ($values['sleep_hours'] === '' || !ctype_digit($values['sleep_hours'])) {
            $errors['sleep_hours'] = 'Enter whole sleep hours.';
        } else {
            $sleepHours = (int) $values['sleep_hours'];
            if ($sleepHours < 0 || $sleepHours > 24) {
                $errors['sleep_hours'] = 'Sleep hours must be from 0 to 24.';
            }
            $values['sleep_hours'] = $sleepHours;
        }

        return [
            'values' => $values,
            'errors' => $errors,
        ];
    }

    /**
     * Checks whether a date matches Y-m-d format.
     */
    private function isValidDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date !== false && $date->format('Y-m-d') === $value;
    }
}
