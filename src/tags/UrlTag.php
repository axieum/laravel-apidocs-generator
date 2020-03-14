<?php

namespace Axieum\ApiDocs\tags;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Type;

final class UrlTag extends TagWithParam
{
    /**
     * @inheritDoc
     */
    public function __construct(string $paramName,
                                ?Type $type = null,
                                bool $required = false,
                                ?Description $description = null,
                                string $name = 'url')
    {
        parent::__construct($paramName, $type, $required, $description, $name);
    }
}
