<?php

namespace Axieum\ApiDocs\tags;

use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Context;
use Webmozart\Assert\Assert;

/**
 * Abstract class that defines an API parameter style tag.
 * Tags should use the syntax: '{@}tag type name optional description'.
 */
abstract class TagWithParam extends TagWithType implements StaticMethod
{
    /** @var string|null parameter name */
    protected $paramName;

    /** @var bool whether the parameter is required */
    protected $required;

    /**
     * Parameter style tag constructor.
     *
     * @param string|null      $paramName   parameter name
     * @param Type|null        $type        parameter value type
     * @param bool             $required    false if the parameter is optional
     * @param Description|null $description tag description
     * @param string           $name        tag name (following '@' sign)
     */
    public function __construct(string $paramName,
                                ?Type $type = null,
                                bool $required = false,
                                ?Description $description = null,
                                string $name = 'parameter')
    {
        $this->name = $name;
        $this->paramName = $paramName;
        $this->required = $required;
        $this->type = $type;
        $this->description = $description;
    }

    /**
     * @inheritDoc
     */
    public static function create(string $body,
                                  string $name = null,
                                  ?TypeResolver $typeResolver = null,
                                  ?DescriptionFactory $descriptionFactory = null,
                                  ?Context $context = null): self
    {
        Assert::stringNotEmpty($name);
        Assert::stringNotEmpty($body);
        Assert::notNull($typeResolver);
        Assert::notNull($descriptionFactory);

        [$typeStr, $body] = self::extractTypeFromBody($body);
        $parts = preg_split('/(\s+)/Su', $body, 3, PREG_SPLIT_DELIM_CAPTURE);
        Assert::isArray($parts); // e.g. ['name', ' ', 'optional', 'description...']

        // param Value Type
        $type = $typeStr ? $typeResolver->resolve($typeStr, $context) : null;

        // param Name
        $paramName = array_shift($parts);
        array_shift($parts);

        // Required/Optional
        $required = false;
        if (isset($parts[0]) && in_array(strtolower($parts[0]), ['required', 'optional'])) {
            $requiredStr = array_shift($parts);
            array_shift($parts);
            $required = strcasecmp($requiredStr, 'required') === 0;
        }

        // Description
        $description = $descriptionFactory->create(implode('', $parts), $context);

        return new static($paramName, $type, $required, $description, $name);
    }

    /**
     * Determines whether the given parameter is required.
     *
     * @return bool true if the parameter is required
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Determines whether the given parameter is optional.
     *
     * @return bool true if the parameter is optional
     */
    public function isOptional(): bool
    {
        return !$this->isRequired();
    }

    /**
     * Returns a string representation of this tag for serialisation purposes.
     *
     * @return string string representation of this tag
     */
    public function __toString(): string
    {
        return ($this->type ? $this->type . ' ' : '')
               . ($this->paramName ? $this->paramName . ' ' : '')
               . ($this->isRequired() ? 'required' : 'optional')
               . ($this->description ? ' ' . $this->description : '');
    }
}
