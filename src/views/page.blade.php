@php
    /** @var string $key group key */
    /** @var \Illuminate\Support\Collection<\Axieum\ApiDocs\util\DocRoute> $routes group routes */
    /** @var string $path output file path */

    /** @var array $group group metadata for current group key */
    $group = $routes->pluck('meta.groups')->collapse()->firstWhere('title', $key);
@endphp

{{-- Markdown Frontmatter --}}
@include('apidocs::frontmatter')

{{-- Page Title and Description --}}
# {{ $group['title'] }}
@isset($group['description']){{ $group['description'] }}@endisset


{{-- Route Listings --}}
@foreach($routes as $route)
@php
    /** @var \Axieum\ApiDocs\util\DocRoute $route */
    $actionDocBlock = $route->getDocBlock('action');
@endphp
## {{ $actionDocBlock->getSummary() ?: "`/{$route->uri()}`" }}
> **{{ join(', ', $route->methods()) }}** `/{{ $route->uri() }}`
@if(!empty((string)$actionDocBlock->getDescription()))

{{ $actionDocBlock->getDescription() }}
@endisset

@endforeach
