<?php

declare(strict_types=1);

/**
 * Stores available values for form fields and sorting.
 */
final class MoodOptions
{
    public const MOODS = [
        'happy' => 'Happy',
        'calm' => 'Calm',
        'sad' => 'Sad',
        'tired' => 'Tired',
    ];

    public const ACTIVITIES = [
        'study' => 'Study',
        'sport' => 'Sport',
        'work' => 'Work',
        'rest' => 'Rest',
    ];

    public const SORT_FIELDS = [
        'created_at' => 'Created at',
        'entry_date' => 'Entry date',
        'author' => 'Author',
        'mood' => 'Mood',
    ];
}
