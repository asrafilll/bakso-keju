<li class="nav-item">
    <a
        href="{!! $href !!}"
        class="nav-link {{ Request::is($activeHref) ? 'active' : '' }}"
    >
        {!! $slot !!}
    </a>
</li>
