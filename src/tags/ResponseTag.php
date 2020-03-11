<?php

namespace Axieum\ApiDocs\tags;

use Illuminate\Http\Response;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\Types\Context;
use Webmozart\Assert\Assert;

final class ResponseTag extends BaseTag implements StaticMethod
{
    /** @var int|null response status code */
    protected $status;

    /**
     * Response tag constructor.
     *
     * @param int              $status      response status code
     * @param Description|null $description tag description
     * @param string           $name        tag name (following '@' sign)
     */
    public function __construct(int $status = null,
                                ?Description $description = null,
                                string $name = 'response')
    {
        $this->name = $name;
        $this->status = $status;
        $this->description = $description;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $body,
                                  string $name = null,
                                  ?DescriptionFactory $descriptionFactory = null,
                                  ?Context $context = null): self
    {
        Assert::stringNotEmpty($name);
        Assert::stringNotEmpty($body);
        Assert::notNull($descriptionFactory);

        $parts = preg_split('/(\s+)/Su', $body, 2, PREG_SPLIT_DELIM_CAPTURE);
        Assert::isArray($parts); // e.g. ['status', ' ', 'json?']

        // Response Status Code
        $status = array_shift($parts);
        if ($status && !preg_match_all('/[0-9]+/', $status)) {
            array_unshift($parts, $status);
            $status = null;
        } else {
            array_shift($parts); // Remove potential space after
        }

        // Description
        $description = $descriptionFactory->create(implode('', $parts), $context);

        return new static($status, $description, $name);
    }

    /**
     * Sets the response's status code.
     *
     * @param int|null $status response status code
     */
    public function setStatus(?int $status): void
    {
        $this->status = $status;
    }

    /**
     * Returns the response's status code.
     *
     * @return int response status code
     */
    public function getStatus(): int
    {
        return $this->status ?: Response::HTTP_OK;
    }

    /**
     * Sets the response's content.
     *
     * @param string|null $content response content
     */
    public function setContent(?string $content): void
    {
        $this->description = new Description($content ?? '');
    }

    /**
     * Returns the response's content.
     *
     * @return string response content (possibly empty)
     */
    public function getContent(): string
    {
        return (string)parent::getDescription();
    }

    /**
     * Returns a string representation of this tag for serialisation purposes.
     *
     * @return string string representation of this tag
     */
    public function __toString(): string
    {
        return ($this->status ? $this->status . ' ' : '')
               . ($this->description ? ' ' . $this->description : '');
    }
}
