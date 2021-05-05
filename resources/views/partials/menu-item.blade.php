@if ($item['submenu'] == [])
    <li class="nav-item">
        @if($item['menutype']=="Laravel")
            <a class="nav-link menu-laravel" href="{{ url($item['link']) }}">{!! $item['icono'] !!} {{ $item['title'] }} </a>
        @else
            <a data-url="{{ route('manager') }}" class="nav-link menu-yi" href="{{ route('manager') }}?url={{ $item['link'] }}">{!! $item['icono'] !!} {{ $item['title'] }} </a>
        @endif
    </li>
@else
    <li class="dropdown" class="nav-item">
        @if($item['menutype']=="Laravel")
            <a href="{{ url($item['link']) }}" class="btn dropdown-toggle linkNav menu-laravel" type="button">{!! $item['icono'] !!} {{ $item['title'] }} <span class="caret"></span></a>
        @else
            <a href="{{ route('manager') }}?url={{ $item['link'] }}" data-url="{{ route('manager') }}" class="btn dropdown-toggle linkNav menu-yi" type="button">{!! $item['icono'] !!} {{ $item['title'] }} <span class="caret"></span></a>
        @endif
        <a href="#" class="btn dropdown-toggle noLinkNav" type="button"> </a>
        <ul class="dropdown-menu sub-menu" aria-labelledby="dropdownMenu2" style="white-space: nowrap;">
            @foreach ($item['submenu'] as $submenu)
                    <li class="nav-item" >
                        @include('partials.menu-item', [ 'item' => $submenu ])
                    </li>
            @endforeach
        </ul>
    </li>
@endif