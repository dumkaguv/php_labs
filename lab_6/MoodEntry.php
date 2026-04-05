<?php

declare(strict_types=1);

/**
 * Represents one diary record submitted through the form.
 */
final class MoodEntry
{
    /**
     * @param string[] $activities
     */
    public function __construct(
        private int $id,
        private string $title,
        private string $author,
        private DateTimeImmutable $entryDate,
        private string $mood,
        private string $energyLevel,
        private float $sleepHours,
        private array $activities,
        private string $gratitude,
        private string $notes,
        private DateTimeImmutable $createdAt
    ) {
    }

    /**
     * Returns the generated entry identifier.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns the short entry title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Returns the author name entered in the form.
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Returns the diary date selected by the user.
     */
    public function getEntryDate(): DateTimeImmutable
    {
        return $this->entryDate;
    }

    /**
     * Returns the raw mood code.
     */
    public function getMood(): string
    {
        return $this->mood;
    }

    /**
     * Returns the human-readable mood label.
     */
    public function getMoodLabel(): string
    {
        return MoodEntryOptions::MOODS[$this->mood] ?? $this->mood;
    }

    /**
     * Returns the raw energy level code.
     */
    public function getEnergyLevel(): string
    {
        return $this->energyLevel;
    }

    /**
     * Returns the human-readable energy level label.
     */
    public function getEnergyLevelLabel(): string
    {
        return MoodEntryOptions::ENERGY_LEVELS[$this->energyLevel] ?? $this->energyLevel;
    }

    /**
     * Returns the number of hours slept.
     */
    public function getSleepHours(): float
    {
        return $this->sleepHours;
    }

    /**
     * Returns the selected activity codes.
     *
     * @return string[]
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /**
     * Returns the gratitude text.
     */
    public function getGratitude(): string
    {
        return $this->gratitude;
    }

    /**
     * Returns the detailed notes text.
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * Returns the creation date stored by the system.
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Converts the entry into an array for JSON storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'entry_date' => $this->entryDate->format('Y-m-d'),
            'mood' => $this->mood,
            'energy_level' => $this->energyLevel,
            'sleep_hours' => $this->sleepHours,
            'activities' => $this->activities,
            'gratitude' => $this->gratitude,
            'notes' => $this->notes,
            'created_at' => $this->createdAt->format(DateTimeInterface::ATOM),
        ];
    }

    /**
     * Restores an entry object from repository data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $activities = [];
        $rawActivities = $data['activities'] ?? [];

        if (is_array($rawActivities)) {
            foreach ($rawActivities as $activity) {
                if (is_scalar($activity)) {
                    $activities[] = (string) $activity;
                }
            }
        }

        return new self(
            (int) ($data['id'] ?? 0),
            (string) ($data['title'] ?? ''),
            (string) ($data['author'] ?? ''),
            new DateTimeImmutable((string) ($data['entry_date'] ?? 'now')),
            (string) ($data['mood'] ?? ''),
            (string) ($data['energy_level'] ?? ''),
            (float) ($data['sleep_hours'] ?? 0),
            array_values(array_unique($activities)),
            (string) ($data['gratitude'] ?? ''),
            (string) ($data['notes'] ?? ''),
            new DateTimeImmutable((string) ($data['created_at'] ?? 'now'))
        );
    }
}
