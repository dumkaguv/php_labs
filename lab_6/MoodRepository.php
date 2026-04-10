<?php

declare(strict_types=1);

/**
 * Saves and loads records from a JSON file.
 */
final class MoodRepository
{
    /**
     * Creates repository with JSON file path.
     */
    public function __construct(
        private string $filePath
    ) {
        $this->prepareStorage();
    }

    /**
     * Saves a record.
     */
    public function save(MoodRecord $record): void
    {
        $items = $this->readRawData();
        $items[] = $record->toArray();

        $json = json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException('Failed to encode data to JSON.');
        }

        file_put_contents($this->filePath, $json . PHP_EOL, LOCK_EX);
    }

    /**
     * Returns all records.
     *
     * @return MoodRecord[]
     */
    public function getAll(): array
    {
        $records = [];

        foreach ($this->readRawData() as $row) {
            $records[] = MoodRecord::fromArray($row);
        }

        return $records;
    }

    /**
     * Returns the next free id.
     */
    public function nextId(): int
    {
        $maxId = 0;

        foreach ($this->getAll() as $record) {
            $maxId = max($maxId, $record->getId());
        }

        return $maxId + 1;
    }

    /**
     * Ensures that storage directory and file exist.
     */
    private function prepareStorage(): void
    {
        $directory = dirname($this->filePath);

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (!is_file($this->filePath)) {
            file_put_contents($this->filePath, "[]\n");
        }
    }

    /**
     * Reads raw arrays from JSON.
     *
     * @return array<int, array<string, mixed>>
     */
    private function readRawData(): array
    {
        $content = file_get_contents($this->filePath);

        if ($content === false || trim($content) === '') {
            return [];
        }

        $data = json_decode($content, true);

        if (!is_array($data)) {
            return [];
        }

        $rows = [];
        foreach ($data as $row) {
            if (is_array($row)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }
}
