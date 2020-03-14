@php
    /** @var \Axieum\ApiDocs\util\DocRoute $route */
    $urlTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\UrlTag::class);
    $queryTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\QueryTag::class);
    $bodyTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\BodyTag::class);
@endphp

### @lang('apidocs::docs.requests.title')

{{-- URL Parameters --}}
@includeWhen($urlTags->isNotEmpty(), 'apidocs::requests.url', ['tags' => $urlTags])
{{-- Query Parameters --}}
@includeWhen($queryTags->isNotEmpty(), 'apidocs::requests.query', ['tags' => $queryTags])
{{-- Body/Form Parameters --}}
@includeWhen($bodyTags->isNotEmpty(), 'apidocs::requests.body', ['tags' => $bodyTags])
