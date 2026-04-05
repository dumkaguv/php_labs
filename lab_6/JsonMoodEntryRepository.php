<?php

declare(strict_types=1);

/**
 * Stores mood entries in a JSON file.
 */
final class JsonMoodEntryRepository implements MoodEntryStorageInterface
{
    /**
     * Creates the repository and prepares the target storage file.
     */
    public function __construct(
        private string $filePath
    ) {
        $this->initializeStorage();
    }

    /**
     * {@inheritDoc}
     */
    public function save(MoodEntry $entry): void
    {
        $entries = $this->readRawEntries();
        $entries[] = $entry->toArray();

        $this->writeRawEntries($entries);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(): array
    {
        $entries = [];

        foreach ($this->readRawEntries() as $entryData) {
            $entries[] = MoodEntry::fromArray($entryData);
        }

        return $entries;
    }

    /**
     * Ensures the directory and JSON file exist before first use.
     */
    private function initializeStorage(): void
    {
        $directory = dirname($this->filePath);

        if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
            throw new RuntimeException('Не удалось создать директорию для хранения данных.');
        }

        if (!is_file($this->filePath)) {
            $this->writeRawEntries([]);
        }
    }

    /**
     * Reads raw decoded rows from the JSON file.
     *
     * @return array<int, array<string, mixed>>
     */
    private function readRawEntries(): array
    {
        $content = file_get_contents($this->filePath);

        if ($content === false || trim($content) === '') {
            return [];
        }

        try {
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException(
                'Не удалось прочитать JSON-файл с данными.',
                0,
                $exception
            );
        }

        if (!is_array($decoded)) {
            return [];
        }

        $entries = [];

        foreach ($decoded as $row) {
            if (is_array($row)) {
                $entries[] = $row;
            }
        }

        return $entries;
    }

    /**
     * Writes raw rows into the JSON file using a readable format.
     *
     * @param array<int, array<string, mixed>> $entries
     */
    private function writeRawEntries(array $entries): void
    {
        try {
            $json = json_encode(
                $entries,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new RuntimeException(
                'Не удалось преобразовать данные в JSON.',
                0,
                $exception
            );
        }

        $writeResult = file_put_contents($this->filePath, $json . PHP_EOL, LOCK_EX);

        if ($writeResult === false) {
            throw new RuntimeException('Не удалось записать данные в файл.');
        }
    }
}
