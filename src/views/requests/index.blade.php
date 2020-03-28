@php
/** @var \Axieum\ApiDocs\util\DocRoute $route */
$urlTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\UrlTag::class);
$queryTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\QueryTag::class);
$bodyTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\BodyTag::class);
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
