<?php

namespace Axieum\ApiDocs\formatter;

/**
 * Documentation Formatter for cleaning up Blade rendered Markdown.
 */
class MarkdownFormatter implements DocFormatter
{
    /**
     * @inheritDoc
     */
    public static function format(string $content): string
    {
        // NB: Blade comments, includes and components all result in
        //     inconsistent line breaks.

        // Add line breaks before titles
        $markdown = preg_replace('/(#+.*)/', "\n$1", $content);

        // Remove line breaks after titles
        $markdown = preg_replace('/(#+.*)\n+/', "$1\n", $markdown);

        // Remove excessive line breaks
        $markdown = preg_replace('/^\n+/m', "\n", $markdown);

        // Trim the content, and end with a blank line
        $markdown = trim($markdown) . "\n";

        return $markdown;
    }
}
