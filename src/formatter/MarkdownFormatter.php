<?php

namespace Axieum\ApiDocs\formatter;

class MarkdownFormatter implements DocFormatter
{
    /**
     * @inheritDoc
     */
    public static function format(string $content): string
    {
        return $content;
    }
}
