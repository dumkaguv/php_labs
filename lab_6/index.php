<?php

declare(strict_types=1);

spl_autoload_register(
    static function (string $class): void {
        $filePath = __DIR__ . '/' . $class . '.php';

        if (is_file($filePath)) {
            require_once $filePath;
        }
    }
);

$repository = new JsonMoodEntryRepository(__DIR__ . '/data/mood_entries.json');
$manager = new MoodEntryManager($repository);
$renderer = new MoodEntryTableRenderer();
$form = new MoodEntryForm();

$sortBy = $manager->normalizeSortField(
    (string) ($_GET['sort'] ?? MoodEntryManager::DEFAULT_SORT_FIELD)
);
$direction = $manager->normalizeSortDirection(
    (string) ($_GET['direction'] ?? MoodEntryManager::DEFAULT_SORT_DIRECTION)
);
$status = (string) ($_GET['status'] ?? '');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $form = new MoodEntryForm($_POST);

    if ($form->validate()) {
        $manager->create($form->getValidatedData());

        $redirectQuery = http_build_query([
            'status' => 'success',
            'sort' => $sortBy,
            'direction' => $direction,
        ]);

        header('Location: index.php?' . $redirectQuery);
        exit;
    }
}

$entries = $manager->getSortedEntries($sortBy, $direction);
$tableHtml = $renderer->render($entries, $sortBy, $direction);
$values = $form->getValues();
$errors = $form->getErrors();
$today = (new DateTimeImmutable('today'))->format('Y-m-d');
$totalEntries = $manager->getTotalEntries();
$hasValidationErrors = $errors !== [];

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Лабораторная работа №6. Дневник настроения</title>
    <style>
        :root {
            --bg: linear-gradient(145deg, #f4efe5 0%, #dce4ec 45%, #f8ddd1 100%);
            --surface: rgba(255, 250, 245, 0.86);
            --surface-strong: rgba(255, 255, 255, 0.72);
            --line: rgba(76, 91, 107, 0.16);
            --text: #233142;
            --muted: #5f6f7f;
            --accent: #b24c2d;
            --accent-soft: rgba(178, 76, 45, 0.12);
            --success: #295f4e;
            --success-soft: rgba(41, 95, 78, 0.12);
            --danger: #a43d47;
            --danger-soft: rgba(164, 61, 71, 0.14);
            --shadow: 0 28px 60px rgba(35, 49, 66, 0.16);
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 32px 18px 40px;
            color: var(--text);
            font-family: "Trebuchet MS", Verdana, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(255, 255, 255, 0.42), transparent 32%),
                radial-gradient(circle at bottom left, rgba(178, 76, 45, 0.18), transparent 28%),
                var(--bg);
        }

        .page {
            width: min(1240px, 100%);
            margin: 0 auto;
            animation: rise-in 0.5s ease;
        }

        .hero {
            margin-bottom: 22px;
            padding: 28px;
            border: 1px solid rgba(255, 255, 255, 0.52);
            border-radius: var(--radius-xl);
            background: var(--surface);
            backdrop-filter: blur(14px);
            box-shadow: var(--shadow);
        }

        .hero__eyebrow {
            display: inline-block;
            margin-bottom: 12px;
            padding: 6px 12px;
            border-radius: 999px;
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: var(--accent-soft);
        }

        .hero__title {
            margin: 0;
            font-family: "Palatino Linotype", "Book Antiqua", Georgia, serif;
            font-size: clamp(34px, 6vw, 54px);
            line-height: 0.98;
        }

        .hero__text {
            max-width: 760px;
            margin: 14px 0 0;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.6;
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(320px, 420px) minmax(0, 1fr);
            gap: 22px;
            align-items: start;
        }

        .card {
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: var(--radius-xl);
            background: var(--surface);
            backdrop-filter: blur(12px);
            box-shadow: var(--shadow);
        }

        .card__body {
            padding: 24px;
        }

        .card__title {
            margin: 0 0 8px;
            font-family: "Palatino Linotype", "Book Antiqua", Georgia, serif;
            font-size: 28px;
        }

        .card__subtitle {
            margin: 0 0 22px;
            color: var(--muted);
            line-height: 1.55;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 22px;
        }

        .summary__item {
            padding: 16px 18px;
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            background: var(--surface-strong);
        }

        .summary__label {
            display: block;
            margin-bottom: 6px;
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .summary__value {
            font-size: 24px;
            font-weight: 700;
        }

        .alert {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: var(--radius-md);
            line-height: 1.5;
        }

        .alert--success {
            border: 1px solid rgba(41, 95, 78, 0.25);
            color: var(--success);
            background: var(--success-soft);
        }

        .alert--danger {
            border: 1px solid rgba(164, 61, 71, 0.22);
            color: var(--danger);
            background: var(--danger-soft);
        }

        .form-grid {
            display: grid;
            gap: 16px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        .field__label {
            font-size: 14px;
            font-weight: 700;
        }

        .field__hint {
            color: var(--muted);
            font-size: 12px;
        }

        .control,
        .textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            color: var(--text);
            font: inherit;
            background: rgba(255, 255, 255, 0.78);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .control:focus,
        .textarea:focus {
            outline: none;
            border-color: rgba(178, 76, 45, 0.4);
            box-shadow: 0 0 0 4px rgba(178, 76, 45, 0.12);
            transform: translateY(-1px);
        }

        .control--error,
        .textarea--error {
            border-color: rgba(164, 61, 71, 0.42);
            box-shadow: 0 0 0 4px rgba(164, 61, 71, 0.08);
        }

        .textarea {
            min-height: 120px;
            resize: vertical;
        }

        .inline-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .checkbox-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .checkbox-card {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.7);
        }

        .checkbox-card input {
            margin-top: 4px;
            accent-color: var(--accent);
        }

        .field__error {
            color: var(--danger);
            font-size: 13px;
        }

        .submit-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 52px;
            padding: 0 20px;
            border: none;
            border-radius: 999px;
            color: #fff8f2;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            background: linear-gradient(135deg, #b24c2d 0%, #7c2d21 100%);
            box-shadow: 0 16px 30px rgba(124, 45, 33, 0.24);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 34px rgba(124, 45, 33, 0.28);
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 18px;
        }

        .toolbar__text {
            color: var(--muted);
            line-height: 1.5;
        }

        .sort-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .sort-form .control {
            min-width: 170px;
        }

        .sort-button {
            min-height: 44px;
            padding: 0 16px;
            border: 1px solid var(--line);
            border-radius: 999px;
            color: var(--text);
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.78);
        }

        .table-wrap {
            overflow-x: auto;
            border: 1px solid var(--line);
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.68);
        }

        .entries-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        .entries-table th,
        .entries-table td {
            padding: 16px 14px;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
            text-align: left;
        }

        .entries-table th {
            color: var(--muted);
            font-size: 12px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            background: rgba(255, 255, 255, 0.84);
        }

        .entries-table tr:nth-child(even) td {
            background: rgba(244, 239, 229, 0.34);
        }

        .entries-table tr:last-child td {
            border-bottom: none;
        }

        .entries-table__empty {
            color: var(--muted);
            text-align: center;
        }

        .sort-link {
            color: inherit;
            text-decoration: none;
        }

        .sort-link--active {
            color: var(--accent);
        }

        .sort-link__arrow {
            font-size: 12px;
        }

        .mood-pill {
            display: inline-flex;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            background: rgba(95, 111, 127, 0.12);
        }

        .mood-pill--happy {
            color: #1f5d4b;
            background: rgba(31, 93, 75, 0.14);
        }

        .mood-pill--calm {
            color: #24507a;
            background: rgba(36, 80, 122, 0.14);
        }

        .mood-pill--neutral {
            color: #6a5d35;
            background: rgba(106, 93, 53, 0.14);
        }

        .mood-pill--tired {
            color: #7b4d29;
            background: rgba(123, 77, 41, 0.14);
        }

        .mood-pill--sad {
            color: #7a3046;
            background: rgba(122, 48, 70, 0.14);
        }

        .activities-cell {
            min-width: 200px;
        }

        .activity-badge {
            display: inline-flex;
            margin: 0 6px 6px 0;
            padding: 6px 10px;
            border-radius: 999px;
            color: var(--accent);
            font-size: 12px;
            font-weight: 700;
            background: var(--accent-soft);
        }

        @keyframes rise-in {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 980px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            body {
                padding-left: 12px;
                padding-right: 12px;
            }

            .hero,
            .card__body {
                padding: 20px;
            }

            .summary,
            .inline-grid,
            .checkbox-grid {
                grid-template-columns: 1fr;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="hero">
            <span class="hero__eyebrow">PHP OOP + формы</span>
            <h1 class="hero__title">Дневник настроения</h1>
            <p class="hero__text">
                Лабораторная работа №6: объектно-ориентированная обработка HTML-форм,
                серверная валидация, сохранение записей в JSON и вывод в сортируемой таблице.
            </p>
        </section>

        <section class="layout">
            <article class="card">
                <div class="card__body">
                    <h2 class="card__title">Новая запись</h2>
                    <p class="card__subtitle">
                        Форма содержит 8 пользовательских полей, включая строковые,
                        числовые, дату, checkbox-группу и длинные текстовые поля.
                    </p>

                    <?php if ($status === 'success'): ?>
                        <div class="alert alert--success">
                            Запись успешно сохранена в файл <code>data/mood_entries.json</code>.
                        </div>
                    <?php endif; ?>

                    <?php if ($hasValidationErrors): ?>
                        <div class="alert alert--danger">
                            Форма не прошла серверную валидацию. Исправьте поля с ошибками и отправьте ее снова.
                        </div>
                    <?php endif; ?>

                    <form class="form-grid" method="post">
                        <div class="field">
                            <label class="field__label" for="title">Заголовок</label>
                            <input
                                class="control<?= isset($errors['title']) ? ' control--error' : '' ?>"
                                id="title"
                                name="title"
                                type="text"
                                value="<?= Html::escape((string) $values['title']) ?>"
                                required
                                minlength="3"
                                maxlength="80"
                                placeholder="Например, Спокойный воскресный вечер"
                            >
                            <span class="field__hint">Короткое название записи, от 3 до 80 символов.</span>
                            <?php if (($message = $form->getFirstError('title')) !== null): ?>
                                <span class="field__error"><?= Html::escape($message) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="inline-grid">
                            <div class="field">
                                <label class="field__label" for="author">Автор</label>
                                <input
                                    class="control<?= isset($errors['author']) ? ' control--error' : '' ?>"
                                    id="author"
                                    name="author"
                                    type="text"
                                    value="<?= Html::escape((string) $values['author']) ?>"
                                    required
                                    minlength="2"
                                    maxlength="40"
                                    placeholder="Введите имя"
                                >
                                <?php if (($message = $form->getFirstError('author')) !== null): ?>
                                    <span class="field__error"><?= Html::escape($message) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field__label" for="entry_date">Дата записи</label>
                                <input
                                    class="control<?= isset($errors['entry_date']) ? ' control--error' : '' ?>"
                                    id="entry_date"
                                    name="entry_date"
                                    type="date"
                                    value="<?= Html::escape((string) $values['entry_date']) ?>"
                                    max="<?= Html::escape($today) ?>"
                                    required
                                >
                                <?php if (($message = $form->getFirstError('entry_date')) !== null): ?>
                                    <span class="field__error"><?= Html::escape($message) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="inline-grid">
                            <div class="field">
                                <label class="field__label" for="mood">Настроение</label>
                                <select
                                    class="control<?= isset($errors['mood']) ? ' control--error' : '' ?>"
                                    id="mood"
                                    name="mood"
                                    required
                                >
                                    <option value="">Выберите настроение</option>
                                    <?php foreach (MoodEntryOptions::MOODS as $value => $label): ?>
                                        <option
                                            value="<?= Html::escape($value) ?>"
                                            <?= (string) $values['mood'] === $value ? 'selected' : '' ?>
                                        >
                                            <?= Html::escape($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (($message = $form->getFirstError('mood')) !== null): ?>
                                    <span class="field__error"><?= Html::escape($message) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field__label" for="energy_level">Уровень энергии</label>
                                <select
                                    class="control<?= isset($errors['energy_level']) ? ' control--error' : '' ?>"
                                    id="energy_level"
                                    name="energy_level"
                                    required
                                >
                                    <option value="">Выберите уровень</option>
                                    <?php foreach (MoodEntryOptions::ENERGY_LEVELS as $value => $label): ?>
                                        <option
                                            value="<?= Html::escape($value) ?>"
                                            <?= (string) $values['energy_level'] === $value ? 'selected' : '' ?>
                                        >
                                            <?= Html::escape($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (($message = $form->getFirstError('energy_level')) !== null): ?>
                                    <span class="field__error"><?= Html::escape($message) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="field">
                            <label class="field__label" for="sleep_hours">Сон за ночь, часов</label>
                            <input
                                class="control<?= isset($errors['sleep_hours']) ? ' control--error' : '' ?>"
                                id="sleep_hours"
                                name="sleep_hours"
                                type="number"
                                value="<?= Html::escape((string) $values['sleep_hours']) ?>"
                                min="0"
                                max="24"
                                step="0.5"
                                required
                                placeholder="Например, 7.5"
                            >
                            <span class="field__hint">Допустимый диапазон от 0 до 24 часов.</span>
                            <?php if (($message = $form->getFirstError('sleep_hours')) !== null): ?>
                                <span class="field__error"><?= Html::escape($message) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="field">
                            <span class="field__label">Активности за день</span>
                            <div class="checkbox-grid">
                                <?php foreach (MoodEntryOptions::ACTIVITIES as $value => $label): ?>
                                    <label class="checkbox-card">
                                        <input
                                            name="activities[]"
                                            type="checkbox"
                                            value="<?= Html::escape($value) ?>"
                                            <?= in_array($value, (array) $values['activities'], true) ? 'checked' : '' ?>
                                        >
                                        <span><?= Html::escape($label) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <span class="field__hint">Выберите от 1 до 4 вариантов.</span>
                            <?php if (($message = $form->getFirstError('activities')) !== null): ?>
                                <span class="field__error"><?= Html::escape($message) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field__label" for="gratitude">За что вы благодарны сегодня</label>
                            <textarea
                                class="textarea<?= isset($errors['gratitude']) ? ' textarea--error' : '' ?>"
                                id="gratitude"
                                name="gratitude"
                                required
                                minlength="10"
                                maxlength="250"
                                placeholder="Короткий позитивный итог дня"
                            ><?= Html::escape((string) $values['gratitude']) ?></textarea>
                            <?php if (($message = $form->getFirstError('gratitude')) !== null): ?>
                                <span class="field__error"><?= Html::escape($message) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field__label" for="notes">Подробные заметки</label>
                            <textarea
                                class="textarea<?= isset($errors['notes']) ? ' textarea--error' : '' ?>"
                                id="notes"
                                name="notes"
                                required
                                minlength="15"
                                maxlength="600"
                                placeholder="Опишите день подробнее: события, эмоции, выводы"
                            ><?= Html::escape((string) $values['notes']) ?></textarea>
                            <?php if (($message = $form->getFirstError('notes')) !== null): ?>
                                <span class="field__error"><?= Html::escape($message) ?></span>
                            <?php endif; ?>
                        </div>

                        <button class="submit-button" type="submit">Сохранить запись</button>
                    </form>
                </div>
            </article>

            <article class="card">
                <div class="card__body">
                    <div class="summary">
                        <div class="summary__item">
                            <span class="summary__label">Всего записей</span>
                            <span class="summary__value"><?= Html::escape((string) $totalEntries) ?></span>
                        </div>
                        <div class="summary__item">
                            <span class="summary__label">Текущая сортировка</span>
                            <span class="summary__value"><?= Html::escape(MoodEntryOptions::SORT_FIELDS[$sortBy]) ?></span>
                        </div>
                    </div>

                    <div class="toolbar">
                        <div class="toolbar__text">
                            Записи читаются из JSON-файла и отображаются в таблице.
                            Сортировку можно менять через форму или кликом по заголовкам колонок.
                        </div>

                        <form class="sort-form" method="get">
                            <select class="control" name="sort">
                                <?php foreach (MoodEntryOptions::SORT_FIELDS as $value => $label): ?>
                                    <option value="<?= Html::escape($value) ?>" <?= $sortBy === $value ? 'selected' : '' ?>>
                                        <?= Html::escape($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select class="control" name="direction">
                                <option value="desc" <?= $direction === 'desc' ? 'selected' : '' ?>>По убыванию</option>
                                <option value="asc" <?= $direction === 'asc' ? 'selected' : '' ?>>По возрастанию</option>
                            </select>

                            <button class="sort-button" type="submit">Применить</button>
                        </form>
                    </div>

                    <div class="table-wrap">
                        <?= $tableHtml ?>
                    </div>
                </div>
            </article>
        </section>
    </main>
</body>
</html>
