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
{{ __('apidocs::docs.authentication') }}
:::
@endif

{{-- Description --}}
@php
    $description = (string) $actionDocBlock->getDescription()
@endphp
@isset($description)
{{ $description }}
@endif

{{-- Example responses --}}
@include('apidocs::responses')
