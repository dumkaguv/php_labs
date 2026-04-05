<?php

declare(strict_types=1);

/**
 * Validates an array of selected values.
 */
final class ArrayChoiceValidator implements ValidatorInterface
{
    /**
     * @param string[] $allowedValues
     */
    public function __construct(
        private array $allowedValues,
        private int $minSelected = 0,
        private ?int $maxSelected = null
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function validate(string $label, mixed $value): array
    {
        if (!is_array($value)) {
            return ["Поле \"$label\" содержит некорректный список значений."];
        }

        $selectedValues = [];

        foreach ($value as $item) {
            if (!is_scalar($item)) {
                return ["Поле \"$label\" содержит некорректный список значений."];
            }

            $selectedValues[] = (string) $item;
        }

        $selectedValues = array_values(array_unique($selectedValues));

        foreach ($selectedValues as $selectedValue) {
            if (!in_array($selectedValue, $this->allowedValues, true)) {
                return ["Поле \"$label\" содержит недопустимое значение."];
            }
        }

        $selectedCount = count($selectedValues);

        if ($selectedCount < $this->minSelected) {
            return ["Выберите минимум {$this->minSelected} вариант(а) для поля \"$label\"."];
        }

        if ($this->maxSelected !== null && $selectedCount > $this->maxSelected) {
            return ["Для поля \"$label\" можно выбрать максимум {$this->maxSelected} вариант(а)."];
        }

        return [];
    }
}
