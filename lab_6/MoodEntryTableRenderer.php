<?php

declare(strict_types=1);

/**
 * Renders stored entries into an HTML table.
 */
final class MoodEntryTableRenderer
{
    /**
     * Builds the HTML markup for the mood diary table.
     *
     * @param MoodEntry[] $entries
     */
    public function render(array $entries, string $sortBy, string $direction): string
    {
        $headerHtml = $this->renderHeader($sortBy, $direction);
        $rowsHtml = $this->renderRows($entries);

        return <<<HTML
<table class="entries-table">
  {$headerHtml}
  <tbody>
    {$rowsHtml}
  </tbody>
</table>
HTML;
    }

    /**
     * Builds the header row with clickable sort links.
     */
    private function renderHeader(string $sortBy, string $direction): string
    {
        $headers = [
            $this->renderSortableHeader('entry_date', 'Дата записи', $sortBy, $direction),
            $this->renderSortableHeader('created_at', 'Создано', $sortBy, $direction),
            $this->renderSortableHeader('author', 'Автор', $sortBy, $direction),
            '<th>Заголовок</th>',
            $this->renderSortableHeader('mood', 'Настроение', $sortBy, $direction),
            '<th>Энергия</th>',
            $this->renderSortableHeader('sleep_hours', 'Сон, ч', $sortBy, $direction),
            '<th>Активности</th>',
            '<th>Благодарность</th>',
            '<th>Заметки</th>',
        ];

        return '<thead><tr>' . implode('', $headers) . '</tr></thead>';
    }

    /**
     * Builds all table body rows.
     *
     * @param MoodEntry[] $entries
     */
    private function renderRows(array $entries): string
    {
        if ($entries === []) {
            return '<tr><td class="entries-table__empty" colspan="10">'
                . 'Пока нет записей. Заполните форму, чтобы добавить первую.'
                . '</td></tr>';
        }

        $rows = [];

        foreach ($entries as $entry) {
            $activityBadges = [];

            foreach ($entry->getActivities() as $activity) {
                $activityLabel = MoodEntryOptions::ACTIVITIES[$activity] ?? $activity;
                $activityBadges[] = '<span class="activity-badge">'
                    . Html::escape($activityLabel)
                    . '</span>';
            }

            $rows[] = '<tr>'
                . '<td>' . Html::escape($entry->getEntryDate()->format('Y-m-d')) . '</td>'
                . '<td>' . Html::escape($entry->getCreatedAt()->format('Y-m-d H:i')) . '</td>'
                . '<td>' . Html::escape($entry->getAuthor()) . '</td>'
                . '<td>' . Html::escape($entry->getTitle()) . '</td>'
                . '<td><span class="mood-pill mood-pill--'
                . Html::escape($entry->getMood())
                . '">'
                . Html::escape($entry->getMoodLabel())
                . '</span></td>'
                . '<td>' . Html::escape($entry->getEnergyLevelLabel()) . '</td>'
                . '<td>' . Html::escape(number_format($entry->getSleepHours(), 1, '.', '')) . '</td>'
                . '<td class="activities-cell">' . implode('', $activityBadges) . '</td>'
                . '<td>' . nl2br(Html::escape($entry->getGratitude())) . '</td>'
                . '<td>' . nl2br(Html::escape($entry->getNotes())) . '</td>'
                . '</tr>';
        }

        return implode('', $rows);
    }

    /**
     * Renders a sortable table header link for one field.
     */
    private function renderSortableHeader(
        string $field,
        string $label,
        string $sortBy,
        string $direction
    ): string {
        $nextDirection = $sortBy === $field && $direction === 'asc'
            ? 'desc'
            : 'asc';
        $arrow = $sortBy === $field
            ? ($direction === 'asc' ? '↑' : '↓')
            : '↕';
        $queryString = http_build_query([
            'sort' => $field,
            'direction' => $nextDirection,
        ]);
        $activeClass = $sortBy === $field ? ' sort-link--active' : '';

        return '<th><a class="sort-link'
            . $activeClass
            . '" href="?'
            . Html::escape($queryString)
            . '">'
            . Html::escape($label)
            . ' <span class="sort-link__arrow">'
            . Html::escape($arrow)
            . '</span></a></th>';
    }
}
