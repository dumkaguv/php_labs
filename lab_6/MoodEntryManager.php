<?php

declare(strict_types=1);

/**
 * Contains business logic for creating and sorting mood entries.
 */
final class MoodEntryManager
{
    public const DEFAULT_SORT_FIELD = 'created_at';
    public const DEFAULT_SORT_DIRECTION = 'desc';

    /**
     * Creates the manager with a storage implementation.
     */
    public function __construct(
        private MoodEntryStorageInterface $repository
    ) {
    }

    /**
     * Creates and stores a new entry from validated form data.
     *
     * @param array{
     *     title: string,
     *     author: string,
     *     entry_date: string,
     *     mood: string,
     *     energy_level: string,
     *     sleep_hours: float,
     *     activities: string[],
     *     gratitude: string,
     *     notes: string
     * } $validatedData
     */
    public function create(array $validatedData): MoodEntry
    {
        $entry = new MoodEntry(
            $this->getNextId(),
            $validatedData['title'],
            $validatedData['author'],
            new DateTimeImmutable($validatedData['entry_date']),
            $validatedData['mood'],
            $validatedData['energy_level'],
            $validatedData['sleep_hours'],
            $validatedData['activities'],
            $validatedData['gratitude'],
            $validatedData['notes'],
            new DateTimeImmutable('now')
        );

        $this->repository->save($entry);

        return $entry;
    }

    /**
     * Returns stored entries sorted by a chosen field and direction.
     *
     * @return MoodEntry[]
     */
    public function getSortedEntries(string $sortBy, string $direction): array
    {
        $entries = $this->repository->getAll();
        $normalizedSortBy = $this->normalizeSortField($sortBy);
        $normalizedDirection = $this->normalizeSortDirection($direction);

        usort(
            $entries,
            function (MoodEntry $left, MoodEntry $right) use (
                $normalizedSortBy,
                $normalizedDirection
            ): int {
                $comparison = $this->compareByField($left, $right, $normalizedSortBy);

                if ($comparison === 0) {
                    $comparison = $left->getId() <=> $right->getId();
                }

                return $normalizedDirection === 'asc'
                    ? $comparison
                    : $comparison * -1;
            }
        );

        return $entries;
    }

    /**
     * Returns the number of stored entries.
     */
    public function getTotalEntries(): int
    {
        return count($this->repository->getAll());
    }

    /**
     * Converts an arbitrary sort field into a safe supported value.
     */
    public function normalizeSortField(string $sortBy): string
    {
        return array_key_exists($sortBy, MoodEntryOptions::SORT_FIELDS)
            ? $sortBy
            : self::DEFAULT_SORT_FIELD;
    }

    /**
     * Converts an arbitrary direction value into either asc or desc.
     */
    public function normalizeSortDirection(string $direction): string
    {
        return strtolower($direction) === 'asc'
            ? 'asc'
            : self::DEFAULT_SORT_DIRECTION;
    }

    /**
     * Generates the next numeric identifier for a new record.
     */
    private function getNextId(): int
    {
        $maxId = 0;

        foreach ($this->repository->getAll() as $entry) {
            $maxId = max($maxId, $entry->getId());
        }

        return $maxId + 1;
    }

    /**
     * Compares two entries by the requested field.
     */
    private function compareByField(
        MoodEntry $left,
        MoodEntry $right,
        string $sortBy
    ): int {
        return match ($sortBy) {
            'entry_date' => $left->getEntryDate() <=> $right->getEntryDate(),
            'mood' => strcmp($left->getMoodLabel(), $right->getMoodLabel()),
            'author' => strcasecmp($left->getAuthor(), $right->getAuthor()),
            'sleep_hours' => $left->getSleepHours() <=> $right->getSleepHours(),
            default => $left->getCreatedAt() <=> $right->getCreatedAt(),
        };
    }
}
