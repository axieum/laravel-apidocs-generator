@include('apidocs::frontmatter')

# {{ $groupName }}

@foreach($routes as $route)
@php /** @var \Axieum\ApiDocs\util\DocRoute $route */ @endphp
## `/{{ $route->getMeta('title', $route->uri()) }}`

@endforeach
