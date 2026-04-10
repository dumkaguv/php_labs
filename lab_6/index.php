<?php

declare(strict_types=1);

require_once __DIR__ . '/Html.php';
require_once __DIR__ . '/MoodOptions.php';
require_once __DIR__ . '/MoodRecord.php';
require_once __DIR__ . '/MoodValidator.php';
require_once __DIR__ . '/MoodRepository.php';
require_once __DIR__ . '/MoodApp.php';

$app = new MoodApp(
    new MoodRepository(__DIR__ . '/data/entries.json'),
    new MoodValidator()
);

$result = $app->handle($_POST, $_GET);
$formData = $result['formData'];
$errors = $result['errors'];
$successMessage = $result['successMessage'];
$records = $result['records'];
$sortBy = $result['sortBy'];
$direction = $result['direction'];

/**
 * Returns the next sorting direction for a column.
 */
function nextDirection(string $currentSort, string $currentDirection, string $field): string
{
    if ($currentSort !== $field) {
        return 'asc';
    }

    return $currentDirection === 'asc' ? 'desc' : 'asc';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 6 Mood Diary</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            color: #333333;
        }

        .container {
            max-width: 960px;
            margin: 20px auto;
            padding: 0 16px;
        }

        .card {
            border: 1px solid #d0d0d0;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #ffffff;
        }

        h1,
        h2 {
            margin-top: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #b5b5b5;
            box-sizing: border-box;
            font-size: 14px;
            background-color: #ffffff;
            color: #333333;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .checkbox-group label {
            display: block;
            font-weight: normal;
            margin-bottom: 6px;
        }

        .checkbox-group input {
            width: auto;
            margin-right: 6px;
        }

        .error {
            color: #666666;
            font-size: 13px;
            margin-top: 4px;
        }

        .success {
            margin-bottom: 16px;
            padding: 10px;
            border: 1px solid #cfcfcf;
            background-color: #f5f5f5;
            color: #444444;
        }

        .button {
            padding: 10px 16px;
            border: 1px solid #b5b5b5;
            background-color: #f3f3f3;
            color: #333333;
            cursor: pointer;
        }

        .toolbar {
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d0d0d0;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f3f3f3;
        }

        th a {
            color: #444444;
            text-decoration: none;
        }

        .muted {
            color: #666666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Lab 6.2: Mood Diary</h1>

            <?php if ($successMessage !== null): ?>
                <div class="success"><?= Html::escape($successMessage) ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="grid">
                    <div>
                        <label for="title">Title</label>
                        <input
                            id="title"
                            name="title"
                            type="text"
                            required
                            minlength="3"
                            maxlength="100"
                            value="<?= Html::escape((string) $formData['title']) ?>"
                        >
                        <?php if (isset($errors['title'])): ?>
                            <div class="error"><?= Html::escape($errors['title']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="author">Author</label>
                        <input
                            id="author"
                            name="author"
                            type="text"
                            required
                            minlength="2"
                            maxlength="60"
                            value="<?= Html::escape((string) $formData['author']) ?>"
                        >
                        <?php if (isset($errors['author'])): ?>
                            <div class="error"><?= Html::escape($errors['author']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="entry_date">Entry date</label>
                        <input
                            id="entry_date"
                            name="entry_date"
                            type="date"
                            required
                            value="<?= Html::escape((string) $formData['entry_date']) ?>"
                        >
                        <?php if (isset($errors['entry_date'])): ?>
                            <div class="error"><?= Html::escape($errors['entry_date']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="sleep_hours">Sleep hours</label>
                        <input
                            id="sleep_hours"
                            name="sleep_hours"
                            type="number"
                            min="0"
                            max="24"
                            required
                            value="<?= Html::escape((string) $formData['sleep_hours']) ?>"
                        >
                        <?php if (isset($errors['sleep_hours'])): ?>
                            <div class="error"><?= Html::escape($errors['sleep_hours']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <label for="mood">Mood</label>
                    <select id="mood" name="mood" required>
                        <option value="">Choose mood</option>
                        <?php foreach (MoodOptions::MOODS as $value => $label): ?>
                            <option
                                value="<?= Html::escape($value) ?>"
                                <?= $formData['mood'] === $value ? 'selected' : '' ?>
                            >
                                <?= Html::escape($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['mood'])): ?>
                        <div class="error"><?= Html::escape($errors['mood']) ?></div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 16px;">
                    <label>Activities</label>
                    <div class="checkbox-group">
                        <?php foreach (MoodOptions::ACTIVITIES as $value => $label): ?>
                            <label>
                                <input
                                    type="checkbox"
                                    name="activities[]"
                                    value="<?= Html::escape($value) ?>"
                                    <?= in_array($value, $formData['activities'], true) ? 'checked' : '' ?>
                                >
                                <?= Html::escape($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (isset($errors['activities'])): ?>
                        <div class="error"><?= Html::escape($errors['activities']) ?></div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 16px;">
                    <label for="notes">Notes</label>
                    <textarea
                        id="notes"
                        name="notes"
                        required
                        minlength="10"
                        maxlength="1000"
                    ><?= Html::escape((string) $formData['notes']) ?></textarea>
                    <?php if (isset($errors['notes'])): ?>
                        <div class="error"><?= Html::escape($errors['notes']) ?></div>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 16px;">
                    <button class="button" type="submit">Save record</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="toolbar">
                <h2>Saved records</h2>
                <div class="muted">Sort by clicking table headers</div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>
                            <a href="?sort=author&amp;direction=<?= Html::escape(nextDirection($sortBy, $direction, 'author')) ?>">Author</a>
                        </th>
                        <th>
                            <a href="?sort=entry_date&amp;direction=<?= Html::escape(nextDirection($sortBy, $direction, 'entry_date')) ?>">Entry date</a>
                        </th>
                        <th>
                            <a href="?sort=mood&amp;direction=<?= Html::escape(nextDirection($sortBy, $direction, 'mood')) ?>">Mood</a>
                        </th>
                        <th>Activities</th>
                        <th>Sleep</th>
                        <th>Notes</th>
                        <th>
                            <a href="?sort=created_at&amp;direction=<?= Html::escape(nextDirection($sortBy, $direction, 'created_at')) ?>">Created at</a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($records === []): ?>
                        <tr>
                            <td colspan="8">No records yet.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $record): ?>
                            <tr>
                                <td><?= Html::escape($record->getTitle()) ?></td>
                                <td><?= Html::escape($record->getAuthor()) ?></td>
                                <td><?= Html::escape($record->getEntryDate()) ?></td>
                                <td><?= Html::escape($record->getMoodLabel()) ?></td>
                                <td><?= Html::escape($record->getActivitiesText()) ?></td>
                                <td><?= Html::escape((string) $record->getSleepHours()) ?></td>
                                <td><?= nl2br(Html::escape($record->getNotes())) ?></td>
                                <td><?= Html::escape($record->getCreatedAt()) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
