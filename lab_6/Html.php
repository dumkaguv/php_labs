<?php

declare(strict_types=1);

/**
 * Contains helper methods for safe HTML output.
 */
final class Html
{
    /**
     * Escapes a value for HTML output.
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
