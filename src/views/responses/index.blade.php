@php
/** @var \Axieum\ApiDocs\util\DocRoute $route */
$responseTags = $route->getTagsByClass(\Axieum\ApiDocs\tags\ResponseTag::class);
@endphp

### {{ __('apidocs::docs.responses.title') }}

{{-- Responses --}}
@foreach($responseTags as $responseTag)
@php
/** @var \Axieum\ApiDocs\tags\ResponseTag $responseTag */
$status = $responseTag->getStatus();
$body = (string) $responseTag->getDescription();
@endphp
**{{ $status }}**:
@if($body)
```{{ $responseTag->language ?? '' }}
{!! $body !!}
```
@else
```
{{ trans_choice('apidocs::docs.responses.status', isset(\Illuminate\Http\Response::$statusTexts[$status]), ['status' => $status, 'text' => \Illuminate\Http\Response::$statusTexts[$status] ?? 'Unknown']) }}
```
@endif

@endforeach

{{-- No Responses --}}
@if($responseTags->isEmpty())
{{ __('apidocs::docs.responses.empty') }}
@endif
