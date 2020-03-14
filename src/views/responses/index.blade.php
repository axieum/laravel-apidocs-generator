@php
    /** @var \Axieum\ApiDocs\util\DocRoute $route */
    $responseTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\ResponseTag::class);
@endphp

### @lang('apidocs::docs.responses.title')

@foreach($responseTags as $responseTag)
@php
    /** @var \Axieum\ApiDocs\tags\ResponseTag $responseTag */
    $status = $responseTag->getStatus();
    $body = (string) $responseTag->getDescription();
@endphp

{{-- Response Status Code --}}
**{{ $status }}**:
{{-- Response Body --}}
@if(!empty($body))
```{{ $responseTag->language ?? '' }}
{!! $body !!}
```
@else
```
@choice('apidocs::docs.responses.status', isset(\Illuminate\Http\Response::$statusTexts[$status]), ['status' => $status, 'text' => \Illuminate\Http\Response::$statusTexts[$status] ?? 'Unknown'])

```

@endif
@endforeach
