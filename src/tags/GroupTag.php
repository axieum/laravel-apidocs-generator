<?php

namespace Axieum\ApiDocs\tags;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\Types\Context;
use Webmozart\Assert\Assert;

final class GroupTag extends BaseTag implements StaticMethod
{
    /**
     * Hidden tag constructor.
     *
     * @param Description|null $description tag description
     * @param string           $name        tag name (following '@' sign)
     */
    public function __construct(?Description $description = null,
                                string $name = 'group')
    {
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @inheritDoc
     */
    public static function create(?string $body,
                                  string $name = 'group',
                                  ?DescriptionFactory $descriptionFactory = null,
                                  ?Context $context = null): self
    {
        Assert::stringNotEmpty($name);
        Assert::notNull($descriptionFactory);

        return new static($descriptionFactory->create($body, $context), $name);
    }

    /**
     * Returns a string representation of this tag for serialisation purposes.
     *
     * @return string string representation of this tag
     */
    public function __toString(): string
    {
        return (string)$this->description;
    }
}
