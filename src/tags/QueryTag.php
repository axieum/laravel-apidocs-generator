<?php

namespace Axieum\ApiDocs\tags;

final class QueryTag extends TagWithParam
{
    /**
     * Returns the query name.
     *
     * @return string query name
     */
    public function getQueryName(): string
    {
        return $this->paramName;
    }
}
