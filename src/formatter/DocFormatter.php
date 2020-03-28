<?php

namespace Axieum\ApiDocs\formatter;

interface DocFormatter
{
    /**
     * Formats the rendered content.
     *
     * @param string $content rendered content
     * @return string formatted content
     */
    public static function format(string $content): string;
}
