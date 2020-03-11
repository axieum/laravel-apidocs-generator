<?php

namespace Axieum\ApiDocs\mutators;

use Axieum\ApiDocs\tags\ResponseTag;
use Axieum\ApiDocs\util\DocRoute;
use DOMDocument;
use Illuminate\Support\Str;

/**
 * Route mutator for formatting and prettifying response content.
 */
class ResponseMutator implements RouteMutator
{
    /**
     * @inheritDoc
     */
    public static function mutate(DocRoute $route): void
    {
        $route->getTagsByClass(ResponseTag::class)
              ->each(function ($responseTag) {
                  /** @var ResponseTag $responseTag */
                  $content = trim($responseTag->getContent());

                  // Bail if no response content is provided
                  if (empty($content)) return;

                  // JSON
                  if (Str::startsWith($content, ['{', '['])) {
                      if ($json = self::prettifyJSON($content)) {
                          /** @noinspection PhpUndefinedFieldInspection inject language property */
                          $responseTag->language = 'json';
                          $responseTag->setContent($json);
                          return;
                      }
                  }

                  // XML
                  if (Str::startsWith($content, '<')) {
                      if ($xml = self::prettifyXML($content)) {
                          /** @noinspection PhpUndefinedFieldInspection inject language property */
                          $responseTag->language = 'xml';
                          $responseTag->setContent($xml);
                          return;
                      }
                  }
              });
    }

    /**
     * Formats and prettifies JSON content.
     *
     * @param string $content JSON content
     * @return string|false prettified JSON or {@code false} on error
     */
    private static function prettifyJSON(string $content)
    {
        // Parse potential JSON content to data structure
        $parsed = json_decode($content);
        // Convert JSON back to string, and hence prettifying
        return $parsed ? json_encode($parsed, JSON_PRETTY_PRINT) : false;
    }

    /**
     * Formats and prettifies XML content.
     *
     * @param string $content XML content
     * @return string|false prettified XML or {@code false} on error
     */
    private static function prettifyXML(string $content)
    {
        // Load and hence parse potential XML content
        $xml = new DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        $xml->loadXML($content, LIBXML_NOXMLDECL | LIBXML_NOERROR | LIBXML_NOWARNING);
        // Convert XML back to string, and hence prettifying
        $xml->encoding = 'UTF-8'; // NB: UTF-8 encoding avoids html entity for symbols
        return $xml->saveXML($xml->documentElement); // NB: `LIBXML_NOXMLDECL` not working - pass document as workaround
    }
}
