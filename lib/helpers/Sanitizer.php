<?php
/**
 * GatewayOS2 Website - Input Sanitizer
 *
 * Static utility methods for cleaning and transforming user input.
 */

class Sanitizer
{
    /**
     * Escape a string for safe HTML output.
     *
     * @param string $str Raw string.
     * @return string HTML-escaped string.
     */
    public static function escape(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Trim whitespace from both ends of a string.
     *
     * @param string $str Raw string.
     * @return string Trimmed string.
     */
    public static function trim(string $str): string
    {
        return trim($str);
    }

    /**
     * Convert a string into a URL-safe slug.
     *
     * Lowercases the string, replaces spaces and underscores with hyphens,
     * removes all characters except alphanumeric and hyphens, and collapses
     * consecutive hyphens.
     *
     * @param string $str Raw string.
     * @return string URL-safe slug.
     */
    public static function slug(string $str): string
    {
        $str = strtolower(trim($str));
        $str = preg_replace('/[\s_]+/', '-', $str);
        $str = preg_replace('/[^a-z0-9\-]/', '', $str);
        $str = preg_replace('/-+/', '-', $str);
        return trim($str, '-');
    }

    /**
     * Strip all HTML and PHP tags from a string.
     *
     * @param string $str Raw string.
     * @return string String with tags removed.
     */
    public static function stripTags(string $str): string
    {
        return strip_tags($str);
    }
}
