<?php

declare(strict_types=1);

function h(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function moods(): array
{
    return [
        'happy' => 'Happy',
        'calm' => 'Calm',
        'sad' => 'Sad',
        'tired' => 'Tired',
    ];
}

function activities(): array
{
    return [
        'study' => 'Study',
        'sport' => 'Sport',
        'work' => 'Work',
        'rest' => 'Rest',
    ];
}

function empty_form_data(): array
{
    return [
        'title' => '',
        'author' => '',
        'entry_date' => date('Y-m-d'),
        'mood' => '',
        'activities' => [],
        'notes' => '',
        'sleep_hours' => '8',
    ];
}

function read_records(string $filePath): array
{
    if (!is_file($filePath)) {
        return [];
    }

    $content = file_get_contents($filePath);
    if ($content === false || trim($content) === '') {
        return [];
    }

    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

function write_records(string $filePath, array $records): void
{
    $directory = dirname($filePath);
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    $json = json_encode($records, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new RuntimeException('Cannot encode records.');
    }

    file_put_contents($filePath, $json . PHP_EOL, LOCK_EX);
}

function next_record_id(array $records): int
{
    $maxId = 0;

    foreach ($records as $record) {
        $maxId = max($maxId, (int) ($record['id'] ?? 0));
    }

    return $maxId + 1;
}

function validate_record(array $post): array
{
    $values = [
        'title' => trim((string) ($post['title'] ?? '')),
        'author' => trim((string) ($post['author'] ?? '')),
        'entry_date' => trim((string) ($post['entry_date'] ?? '')),
        'mood' => trim((string) ($post['mood'] ?? '')),
        'activities' => is_array($post['activities'] ?? null) ? array_values($post['activities']) : [],
        'notes' => trim((string) ($post['notes'] ?? '')),
        'sleep_hours' => trim((string) ($post['sleep_hours'] ?? '')),
    ];

    $errors = [];

    if ($values['title'] === '') {
        $errors['title'] = 'Enter a title.';
    } elseif (strlen($values['title']) < 3) {
        $errors['title'] = 'Title must contain at least 3 characters.';
    }

    if ($values['author'] === '') {
        $errors['author'] = 'Enter an author.';
    } elseif (strlen($values['author']) < 2) {
        $errors['author'] = 'Author must contain at least 2 characters.';
    }

    $date = DateTimeImmutable::createFromFormat('Y-m-d', $values['entry_date']);
    if ($date === false || $date->format('Y-m-d') !== $values['entry_date']) {
        $errors['entry_date'] = 'Enter a valid date.';
    }

    if (!array_key_exists($values['mood'], moods())) {
        $errors['mood'] = 'Choose a mood.';
    }

    $validActivities = [];
    foreach ($values['activities'] as $activity) {
        if (is_string($activity) && array_key_exists($activity, activities())) {
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

    return ['values' => $values, 'errors' => $errors];
}

function prepare_record_for_view(array $record): array
{
    $activityLabels = [];
    foreach (($record['activities'] ?? []) as $activity) {
        if (is_string($activity)) {
            $activityLabels[] = activities()[$activity] ?? $activity;
        }
    }

    $mood = (string) ($record['mood'] ?? '');

    return [
        'id' => (int) ($record['id'] ?? 0),
        'title' => (string) ($record['title'] ?? ''),
        'author' => (string) ($record['author'] ?? ''),
        'entry_date' => (string) ($record['entry_date'] ?? ''),
        'mood' => $mood,
        'mood_label' => moods()[$mood] ?? $mood,
        'activities_text' => implode(', ', $activityLabels),
        'notes' => (string) ($record['notes'] ?? ''),
        'sleep_hours' => (int) ($record['sleep_hours'] ?? 0),
        'created_at' => (string) ($record['created_at'] ?? ''),
    ];
}

function sort_records(array $records, string $sortBy, string $direction): array
{
    usort($records, function (array $left, array $right) use ($sortBy, $direction): int {
        $comparison = match ($sortBy) {
            'author', 'mood' => strcasecmp((string) ($left[$sortBy] ?? ''), (string) ($right[$sortBy] ?? '')),
            'entry_date' => strcmp((string) ($left['entry_date'] ?? ''), (string) ($right['entry_date'] ?? '')),
            default => strcmp((string) ($left['created_at'] ?? ''), (string) ($right['created_at'] ?? '')),
        };

        return $direction === 'asc' ? $comparison : -$comparison;
    });

    return $records;
}

function next_direction(string $currentSort, string $currentDirection, string $field): string
{
    if ($currentSort !== $field) {
        return 'asc';
    }

    return $currentDirection === 'asc' ? 'desc' : 'asc';
}

function sort_links(string $sortBy, string $direction, string $baseUrl): array
{
    $fields = [
        'author' => 'Author',
        'entry_date' => 'Entry date',
        'mood' => 'Mood',
        'created_at' => 'Created at',
    ];

    $links = [];
    foreach ($fields as $field => $label) {
        $nextDirection = next_direction($sortBy, $direction, $field);
        $links[$field] = [
            'label' => $label,
            'url' => $baseUrl . '?sort=' . urlencode($field) . '&direction=' . urlencode($nextDirection),
        ];
    }

    return $links;
}

function format_sleep(int $hours): string
{
    return $hours . ' h';
}
