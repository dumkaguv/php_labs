<?php

declare(strict_types=1);

/**
 * Coordinates form processing and record sorting.
 */
final class MoodApp
{
    /**
     * Creates application services.
     */
    public function __construct(
        private MoodRepository $repository,
        private MoodValidator $validator
    ) {
    }

    /**
     * Handles current request.
     *
     * @param array<string, mixed> $post
     * @param array<string, mixed> $get
     * @return array{
     *     formData: array<string, mixed>,
     *     errors: array<string, string>,
     *     successMessage: string|null,
     *     records: array<int, MoodRecord>,
     *     sortBy: string,
     *     direction: string
     * }
     */
    public function handle(array $post, array $get): array
    {
        $formData = [
            'title' => '',
            'author' => '',
            'entry_date' => date('Y-m-d'),
            'mood' => '',
            'activities' => [],
            'notes' => '',
            'sleep_hours' => '8',
        ];

        $errors = [];
        $successMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->validator->validate($post);
            $formData = $result['values'];
            $errors = $result['errors'];

            if ($errors === []) {
                $record = new MoodRecord(
                    $this->repository->nextId(),
                    (string) $formData['title'],
                    (string) $formData['author'],
                    (string) $formData['entry_date'],
                    (string) $formData['mood'],
                    $formData['activities'],
                    (string) $formData['notes'],
                    date('Y-m-d H:i:s'),
                    (int) $formData['sleep_hours']
                );

                $this->repository->save($record);
                $successMessage = 'Record saved successfully.';

                $formData = [
                    'title' => '',
                    'author' => '',
                    'entry_date' => date('Y-m-d'),
                    'mood' => '',
                    'activities' => [],
                    'notes' => '',
                    'sleep_hours' => '8',
                ];
            }
        }

        $sortBy = $this->normalizeSortBy((string) ($get['sort'] ?? 'created_at'));
        $direction = $this->normalizeDirection((string) ($get['direction'] ?? 'desc'));
        $records = $this->getSortedRecords($sortBy, $direction);

        return [
            'formData' => $formData,
            'errors' => $errors,
            'successMessage' => $successMessage,
            'records' => $records,
            'sortBy' => $sortBy,
            'direction' => $direction,
        ];
    }

    /**
     * Returns records sorted by selected field.
     *
     * @return MoodRecord[]
     */
    private function getSortedRecords(string $sortBy, string $direction): array
    {
        $records = $this->repository->getAll();

        usort(
            $records,
            function (MoodRecord $left, MoodRecord $right) use ($sortBy, $direction): int {
                $comparison = match ($sortBy) {
                    'entry_date' => strcmp($left->getEntryDate(), $right->getEntryDate()),
                    'author' => strcasecmp($left->getAuthor(), $right->getAuthor()),
                    'mood' => strcasecmp($left->getMoodLabel(), $right->getMoodLabel()),
                    default => strcmp($left->getCreatedAt(), $right->getCreatedAt()),
                };

                return $direction === 'asc' ? $comparison : $comparison * -1;
            }
        );

        return $records;
    }

    /**
     * Normalizes sort field.
     */
    private function normalizeSortBy(string $sortBy): string
    {
        return array_key_exists($sortBy, MoodOptions::SORT_FIELDS) ? $sortBy : 'created_at';
    }

    /**
     * Normalizes sort direction.
     */
    private function normalizeDirection(string $direction): string
    {
        return strtolower($direction) === 'asc' ? 'asc' : 'desc';
    }
}
