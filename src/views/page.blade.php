@include('frontmatter')

<ul>
    @for($i=0;$i<5;$i++)
        <li>{{ $i }}</li>
    @endfor
</ul>
