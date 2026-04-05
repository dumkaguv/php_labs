<?php

declare(strict_types=1);

/**
 * Provides small helpers for safe HTML output.
 */
final class Html
{
    /**
     * Escapes a value for safe rendering inside HTML.
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
