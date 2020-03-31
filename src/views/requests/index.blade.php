@php
/** @var \Axieum\ApiDocs\util\DocRoute $route */

// Fetch all unique (on parameter name) Parameter tags
/** @noinspection PhpUndefinedMethodInspection higher order collection proxy used */
$urlTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\UrlTag::class)->unique->getParamName();
$queryTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\QueryTag::class)->unique->getParamName();
$bodyTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\BodyTag::class)->unique->getParamName();
@endphp

### {{ __('apidocs::docs.requests.title') }}

@if($urlTags->isNotEmpty() || $queryTags->isNotEmpty() || $bodyTags->isNotEmpty())
{{-- URL Parameters --}}
@includeWhen($urlTags->isNotEmpty(), 'apidocs::requests.url', ['tags' => $urlTags])
{{-- Query Parameters --}}
@includeWhen($queryTags->isNotEmpty(), 'apidocs::requests.query', ['tags' => $queryTags])
{{-- Body/Form Parameters --}}
@includeWhen($bodyTags->isNotEmpty(), 'apidocs::requests.body', ['tags' => $bodyTags])
@else
{{-- No Request Parameters --}}
{{ __('apidocs::docs.requests.empty') }}
@endif
