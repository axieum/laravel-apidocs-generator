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
    /** @var string $title group title */
    protected $title;

    /**
     * Group tag constructor.
     *
     * @param string           $title       group title
     * @param Description|null $description tag description
     * @param string           $name        tag name (following '@' sign)
     */
    public function __construct(string $title,
                                ?Description $description = null,
                                string $name = 'group')
    {
        $this->name = $name;
        $this->title = $title;
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

        $parts = preg_split('/(\n+)/Su', $body, 2, PREG_SPLIT_DELIM_CAPTURE);

        // Title
        $title = array_shift($parts);
        Assert::stringNotEmpty($title);

        // Description
        $description = $descriptionFactory->create(trim(implode('', $parts)), $context);

        return new static($title, $description, $name);
    }

    /**
     * Returns the group title.
     *
     * @return string group title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Returns a string representation of this tag for serialisation purposes.
     *
     * @return string string representation of this tag
     */
    public function __toString(): string
    {
        return $this->title . PHP_EOL . (string)$this->description;
    }
}
