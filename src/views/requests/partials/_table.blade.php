@unless(empty($tags))
| Name | Type | Required | Description |
|------|------|----------|-------------|
@foreach($tags as $tag)
| {{ $tag->getParamName() }} | {{ $tag->getType() }} | @choice('apidocs::docs.requests.parameters.required', (int) $tag->isRequired()) | {{ $tag->getDescription() }} |
@endforeach
@endunless
