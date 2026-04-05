<?php

declare(strict_types=1);

/**
 * Stores fixed option sets used by the form and table.
 */
final class MoodEntryOptions
{
    public const MOODS = [
        'happy' => 'Отличное',
        'calm' => 'Спокойное',
        'neutral' => 'Нейтральное',
        'tired' => 'Уставшее',
        'sad' => 'Грустное',
    ];

    public const ENERGY_LEVELS = [
        'high' => 'Высокий',
        'medium' => 'Средний',
        'low' => 'Низкий',
    ];

    public const ACTIVITIES = [
        'study' => 'Учеба',
        'sport' => 'Спорт',
        'work' => 'Работа',
        'friends' => 'Встреча с друзьями',
        'rest' => 'Отдых',
        'reading' => 'Чтение',
    ];

    public const SORT_FIELDS = [
        'created_at' => 'По дате создания',
        'entry_date' => 'По дате записи',
        'mood' => 'По настроению',
        'author' => 'По автору',
        'sleep_hours' => 'По часам сна',
    ];
}
