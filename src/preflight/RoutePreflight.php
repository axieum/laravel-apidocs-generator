<?php

namespace Axieum\ApiDocs\preflight;

use Illuminate\Routing\Route;

interface RoutePreflight
{
    /**
     * Returns a logging level of this preflight check.
     *
     * @return string degree of logging - {@see PreflightDegree}
     */
    public static function degree(): string;

    /**
     * Checks a given route to determine if API Documentation can/should be generated.
     *
     * @param Route $route matched route for documentation generation
     * @return string|null failure reason or null if filter passed
     */
    public static function apply(Route $route): ?string;
}

abstract class PreflightDegree
{
    const DEFAULT = 'line';
    const INFO = 'info';
    const WARNING = 'warn';
    const ERROR = 'error';
    const ALERT = 'alert';

    /**
     * Returns all valid preflight degrees.
     *
     * @return array<string> preflight degrees
     */
    public static function values(): array
    {
        return [self::DEFAULT,
                self::INFO,
                self::WARNING,
                self::ERROR,
                self::ALERT];
    }
}
