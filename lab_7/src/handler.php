<?php

declare(strict_types=1);

function handle_mood_request(string $dataFile): array
{
    $records = read_records($dataFile);
    $formData = empty_form_data();
    $errors = [];
    $successMessage = null;

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $result = validate_record($_POST);
        $formData = $result['values'];
        $errors = $result['errors'];

        if ($errors === []) {
            $records[] = [
                'id' => next_record_id($records),
                'title' => $formData['title'],
                'author' => $formData['author'],
                'entry_date' => $formData['entry_date'],
                'mood' => $formData['mood'],
                'activities' => $formData['activities'],
                'notes' => $formData['notes'],
                'sleep_hours' => (int) $formData['sleep_hours'],
                'created_at' => date('Y-m-d H:i:s'),
            ];

            write_records($dataFile, $records);

            $successMessage = 'Record saved successfully.';
            $formData = empty_form_data();
        }
    }

    $allowedSorts = ['created_at', 'entry_date', 'author', 'mood'];
    $sortBy = in_array(($_GET['sort'] ?? ''), $allowedSorts, true) ? (string) $_GET['sort'] : 'created_at';
    $direction = ($_GET['direction'] ?? '') === 'asc' ? 'asc' : 'desc';
    $baseUrl = basename((string) ($_SERVER['SCRIPT_NAME'] ?? 'index.php'));

    $records = sort_records($records, $sortBy, $direction);

    return [
        'page_title' => 'Lab 7. Mood Diary',
        'form_data' => $formData,
        'errors' => $errors,
        'success_message' => $successMessage,
        'records' => array_map('prepare_record_for_view', $records),
        'moods' => moods(),
        'activities' => activities(),
        'sort_by' => $sortBy,
        'direction' => $direction,
        'sort_links' => sort_links($sortBy, $direction, $baseUrl),
        'base_url' => $baseUrl,
    ];
}
