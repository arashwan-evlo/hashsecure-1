<ul>
    @foreach($childs as $child)
        <li>
            {{ $child->name }}
            @if(count($child->childs))
                @include('chartofaccounts::manageChild',['childs' => $child->childs])
            @endif
        </li>
    @endforeach

</ul>