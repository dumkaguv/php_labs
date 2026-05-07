<?php

/**
 * @var array<int, array{
 *     title: string,
 *     author: string,
 *     entry_date: string,
 *     mood_label: string,
 *     activities_text: string,
 *     sleep_hours: int,
 *     notes: string,
 *     created_at: string
 * }> $records
 * @var array<string, array{label: string, url: string}> $sort_links
 */
?>
<section class="card">
    <h2>Saved records</h2>
    <p class="muted">Click table headers to sort records.</p>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th><a href="<?= h($sort_links['author']['url']) ?>">Author</a></th>
                <th><a href="<?= h($sort_links['entry_date']['url']) ?>">Entry date</a></th>
                <th><a href="<?= h($sort_links['mood']['url']) ?>">Mood</a></th>
                <th>Activities</th>
                <th>Sleep</th>
                <th>Notes</th>
                <th><a href="<?= h($sort_links['created_at']['url']) ?>">Created at</a></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($records === []): ?>
                <tr>
                    <td colspan="8">No records yet.</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= h($record['title']) ?></td>
                    <td><?= h($record['author']) ?></td>
                    <td><?= h($record['entry_date']) ?></td>
                    <td><?= h($record['mood_label']) ?></td>
                    <td><?= h($record['activities_text']) ?></td>
                    <td><?= h(format_sleep($record['sleep_hours'])) ?></td>
                    <td><?= nl2br(h($record['notes'])) ?></td>
                    <td><?= h($record['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
