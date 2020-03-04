<?php

namespace Axieum\ApiDocs\tags;

final class UrlTag extends TagWithParam
{
    /**
     * Returns the url parameter name.
     *
     * @return string url parameter name
     */
    public function getUrlParamName(): string
    {
        return $this->paramName;
    }
}
