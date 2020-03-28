| Name | Type | Required | Description |
|------|------|----------|-------------|
@foreach($tags as $tag)
| {{ $tag->getParamName() }} | {{ $tag->getType() }} | {{ trans_choice('apidocs::docs.requests.parameters.required', $tag->isRequired()) }} | {{ $tag->getDescription() }} |
@endforeach
