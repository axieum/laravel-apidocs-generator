@include('apidocs::frontmatter')

<ul>
    @foreach($actionDocBlock->getTags() as $tag)
        <li>{{ (string) $tag  }}</li>
    @endforeach
</ul>
