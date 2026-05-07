<?php

/**
 * @var array{
 *     title: string,
 *     author: string,
 *     entry_date: string,
 *     mood: string,
 *     activities: string[],
 *     notes: string,
 *     sleep_hours: string|int
 * } $form_data
 * @var array<string, string> $errors
 * @var string|null $success_message
 * @var array<string, string> $moods
 * @var array<string, string> $activities
 * @var string $base_url
 */
?>
<section class="card">
    <h2>Add record</h2>

    <?php if ($success_message !== null): ?>
        <div class="success"><?= h($success_message) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= h($base_url) ?>">
        <div class="grid">
            <div class="field">
                <label for="title">Title</label>
                <input id="title" name="title" type="text" value="<?= h($form_data['title']) ?>">
                <?php if (isset($errors['title'])): ?>
                    <div class="error"><?= h($errors['title']) ?></div>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="author">Author</label>
                <input id="author" name="author" type="text" value="<?= h($form_data['author']) ?>">
                <?php if (isset($errors['author'])): ?>
                    <div class="error"><?= h($errors['author']) ?></div>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="entry_date">Entry date</label>
                <input id="entry_date" name="entry_date" type="date" value="<?= h($form_data['entry_date']) ?>">
                <?php if (isset($errors['entry_date'])): ?>
                    <div class="error"><?= h($errors['entry_date']) ?></div>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="sleep_hours">Sleep hours</label>
                <input id="sleep_hours" name="sleep_hours" type="number" min="0" max="24" value="<?= h($form_data['sleep_hours']) ?>">
                <?php if (isset($errors['sleep_hours'])): ?>
                    <div class="error"><?= h($errors['sleep_hours']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="field">
            <label for="mood">Mood</label>
            <select id="mood" name="mood">
                <option value="">Choose mood</option>
                <?php foreach ($moods as $value => $label): ?>
                    <option value="<?= h($value) ?>" <?= $form_data['mood'] === $value ? 'selected' : '' ?>>
                        <?= h($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['mood'])): ?>
                <div class="error"><?= h($errors['mood']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field checkbox">
            <label>Activities</label>
            <?php foreach ($activities as $value => $label): ?>
                <label>
                    <input
                        type="checkbox"
                        name="activities[]"
                        value="<?= h($value) ?>"
                        <?= in_array($value, $form_data['activities'], true) ? 'checked' : '' ?>
                    >
                    <?= h($label) ?>
                </label>
            <?php endforeach; ?>
            <?php if (isset($errors['activities'])): ?>
                <div class="error"><?= h($errors['activities']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="notes">Notes</label>
            <textarea id="notes" name="notes"><?= h($form_data['notes']) ?></textarea>
            <?php if (isset($errors['notes'])): ?>
                <div class="error"><?= h($errors['notes']) ?></div>
            <?php endif; ?>
        </div>

        <button class="button" type="submit">Save record</button>
    </form>
</section>
