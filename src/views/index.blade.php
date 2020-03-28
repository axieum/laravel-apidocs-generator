@php
/** @var string $key group key */
/** @var \Illuminate\Support\Collection<\Axieum\ApiDocs\util\DocRoute> $routes group routes */
/** @var string $path output file path */

/** @var array|null $group group metadata for current group key (specific for groupings by 'meta.groups.*.title') */
$group = $routes->pluck('meta.groups')->collapse()->firstWhere('title', $key);
@endphp

{{-- Markdown Frontmatter --}}
@include('apidocs::frontmatter')

{{-- Header --}}
@include('apidocs::header')

{{-- Route Listings --}}
@each('apidocs::route', $routes, 'route')

{{-- Footer --}}
@include('apidocs::footer')
