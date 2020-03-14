<?php

namespace Axieum\ApiDocs\tags;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Type;

final class QueryTag extends TagWithParam
{
    /**
     * @inheritDoc
     */
    public function __construct(string $paramName,
                                ?Type $type = null,
                                bool $required = false,
                                ?Description $description = null,
                                string $name = 'query')
    {
        parent::__construct($paramName, $type, $required, $description, $name);
    }
}
