@php
/** @var \Axieum\ApiDocs\util\DocRoute $route */
$actionDocBlock = $route->getDocBlock('action');
$description = (string) $actionDocBlock->getDescription();
@endphp

{{-- Title and URI --}}
## {{ $actionDocBlock->getSummary() ?: "`/{$route->uri()}`" }}
> **{{ join(', ', $route->methods()) }}** `/{{ $route->uri() }}`

{{-- Description --}}
{{ $description }}

{{-- Request Parameters --}}
@include('apidocs::requests.index')

{{-- Example Responses --}}
@include('apidocs::responses.index')
