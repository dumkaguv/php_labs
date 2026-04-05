<?php

declare(strict_types=1);

/**
 * Describes the storage contract for mood entries.
 */
interface MoodEntryStorageInterface
{
    /**
     * Saves a new mood entry.
     */
    public function save(MoodEntry $entry): void;

    /**
     * Returns all stored mood entries.
     *
     * @return MoodEntry[]
     */
    public function getAll(): array;
}
