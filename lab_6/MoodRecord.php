<?php

declare(strict_types=1);

/**
 * Represents one mood diary record.
 */
final class MoodRecord
{
    /**
     * @param string[] $activities
     */
    public function __construct(
        private int $id,
        private string $title,
        private string $author,
        private string $entryDate,
        private string $mood,
        private array $activities,
        private string $notes,
        private string $createdAt,
        private int $sleepHours
    ) {
    }

    /**
     * Returns record id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Returns title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Returns author.
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Returns entry date.
     */
    public function getEntryDate(): string
    {
        return $this->entryDate;
    }

    /**
     * Returns mood code.
     */
    public function getMood(): string
    {
        return $this->mood;
    }

    /**
     * Returns mood label.
     */
    public function getMoodLabel(): string
    {
        return MoodOptions::MOODS[$this->mood] ?? $this->mood;
    }

    /**
     * Returns selected activities.
     *
     * @return string[]
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /**
     * Returns activities as text.
     */
    public function getActivitiesText(): string
    {
        $labels = [];

        foreach ($this->activities as $activity) {
            $labels[] = MoodOptions::ACTIVITIES[$activity] ?? $activity;
        }

        return implode(', ', $labels);
    }

    /**
     * Returns notes.
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * Returns created at value.
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Returns sleep hours.
     */
    public function getSleepHours(): int
    {
        return $this->sleepHours;
    }

    /**
     * Converts object to array for JSON storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'entry_date' => $this->entryDate,
            'mood' => $this->mood,
            'activities' => $this->activities,
            'notes' => $this->notes,
            'created_at' => $this->createdAt,
            'sleep_hours' => $this->sleepHours,
        ];
    }

    /**
     * Creates object from repository array data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $activities = [];

        if (isset($data['activities']) && is_array($data['activities'])) {
            foreach ($data['activities'] as $activity) {
                if (is_string($activity)) {
                    $activities[] = $activity;
                }
            }
        }

        return new self(
            (int) ($data['id'] ?? 0),
            (string) ($data['title'] ?? ''),
            (string) ($data['author'] ?? ''),
            (string) ($data['entry_date'] ?? ''),
            (string) ($data['mood'] ?? ''),
            $activities,
            (string) ($data['notes'] ?? ''),
            (string) ($data['created_at'] ?? ''),
            (int) ($data['sleep_hours'] ?? 0)
        );
    }
}
