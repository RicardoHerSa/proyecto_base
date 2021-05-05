@if ($item['submenu'] == [])
    <li>
        @if($item['menutype']=="Laravel")
           <a class="nav-link menu-laravel" href="{{ url($item['link']) }}">&nbsp;&nbsp; {!! $item['icono'] !!}{{ $item['title'] }} </a>
        @else
            <a data-url="{{ route('manager') }}" class="nav-link menu-yi" href="{{ route('manager') }}?url={{ $item['link'] }}">{!! $item['icono'] !!}{{ $item['title'] }} </a>
        @endif
    </li>
@else
    <li>
        @if($item['menutype']=="Laravel")
            <a href="#{{ $item['alias'] }}" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle col-sm-12">
                <span class="linkTitle" data-href="{{ url($item['link']) }}">
                {!! $item['icono'] !!} {{ $item['title'] }}
                <span>
                <span class="caret"></span></a>
        @else
            <a href="#{{ $item['alias'] }}" data-url="{{ route('manager') }}" class="dropdown-toggle col-sm-12" data-toggle="collapse" aria-expanded="false">
                <span class="linkTitle" data-href="{{ route('manager') }}?url={{ $item['link'] }}">
                {!! $item['icono'] !!} {{ $item['title'] }} 
                </span>
                <span class="caret"></span></a>
        @endif

        <ul class="collapse list-unstyled" id="{{ $item['alias'] }}">
            @foreach ($item['submenu'] as $submenu)
                    <li>
                        @include('partials.menu-nav', [ 'item' => $submenu ])
                    </li>
            @endforeach
        </ul>
    </li>
@endif