@php
    /** @var \Axieum\ApiDocs\util\DocRoute $route */
    $actionDocBlock = $route->getDocBlock('action');
@endphp

{{-- Title and URI --}}
## {{ $actionDocBlock->getSummary() ?: "`/{$route->uri()}`" }}
> **{{ join(', ', $route->methods()) }}** `/{{ $route->uri() }}`

{{-- Tags --}}
@if($route->getMeta('auth'))
:::info
@lang('apidocs::docs.tags.auth')

:::
@endif

{{-- Description --}}
@php
    $description = (string) $actionDocBlock->getDescription()
@endphp
@isset($description){{ $description }}@endif

{{-- Request Parameters --}}
@include('apidocs::requests.index')

{{-- Example responses --}}
@include('apidocs::responses.index')
