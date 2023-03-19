<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a
                class="nav-link"
                data-widget="pushmenu"
                href="#"
                role="button"
            ><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a
                class="nav-link"
                data-toggle="dropdown"
                href="#"
            >
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a
                    href="#"
                    class="dropdown-item"
                >
                    <span>4 new messages</span>
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a
                    href="#"
                    class="dropdown-item"
                >
                    <span>8 friend requests</span>
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a
                    href="#"
                    class="dropdown-item"
                >
                    <span>3 new reports</span>
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a
                    href="#"
                    class="dropdown-item dropdown-footer"
                >See All Notifications</a>
            </div>
        </li>
        <li class="nav-item dropdown">
            <a
                class="nav-link"
                data-toggle="dropdown"
                href="#"
            >
                <img
                    src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAAAXNSR0IArs4c6QAAAnlJREFUaEPtmUmLIkEQhcN9RwT3dkNwQdz+/2/w4EXcULvdUQRFcUFth5eDTqkzTdtWHzon4lSYWRn14nsReVBTKpVO9B+FhgVLTpsJSw6YmDATlqwCbGnJgN7JYcJMWLIKsKUlA8pDiy3NlpasAmxpyYDylGZLs6UVFdBoNBSJRMjlctHxeKThcEiz2UzscDqdFA6HyWAw0Gq1otfXV9rv91+q33fmecjSwWCQzGazEGOxWCgajVKtViN8YDabpU6nQ8vlkl5eXshoNFK73f6S4O/M85DgXC5HzWaTttvtlRAQd7vdYg2h0+kon89TuVym0+nPHxs+n4/sdju1Wi2xL5FI0Hw+p+l0enXes3k+qvKnBUNEoVCgfr9PXq+X3t/fhaXxwYFAgPR6PfV6vUsuCK7X67Tb7S6/wQnpdJoGgwFptVry+/3CIcpQI48qgmFR2BYix+Mx2Ww2QahSqYgCQAyKcQ5QAsn1en2V32q1UiwWE/th+c1mc7WuVp5/iX6IcLFYFDbFwEIkk0lhR5PJJHq22+1e8mBvtVq9InxeTKVS4hEOuA0QVivP30R/WjBevhUBwZPJRNDyeDzUaDREDkxquOG2h8/THFbGO6PRiBaLxd13qZHnacI4ANcOCLy9vRGsebY0+lk5pc/7MLWVgb7NZDLC6niOx+OiJfC+Mp7No0oP4xCIRf85HA46HA6iZzG0EPgN1xTo4mrC1YU9ygiFQoLsebhhP8Qqh50aeVQT/NFBP2XtoR7+KaKYsKICTFgG27Kl2dKy+5gJM2F5K8D3sLxsfytjwkxYsgqwpSUDeieHCTNhySrAlpYMKA8ttjRbWrIK/AIA6DmP8pRfKAAAAABJRU5ErkJggg=="
                    class="img-circle"
                    alt="User"
                    height="100%"
                />
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item">{{ Auth::user()->name }}</span>
                <div class="dropdown-divider"></div>
                <a
                    href="{{ url('/profile') }}"
                    class="dropdown-item"
                >{{ __('Your profile') }}</a>
                <a
                    href="{{ url('/profile/password') }}"
                    class="dropdown-item"
                >{{ __('Change Password') }}</a>
                <div class="dropdown-divider"></div>
                <a
                    href="#"
                    class="dropdown-item"
                    onclick="document.getElementById('signout').click()"
                >{{ __('Sign out') }}</a>
                <form
                    action="{{ url('/auth/signout') }}"
                    method="POST"
                    style="display: none;"
                >
                    @csrf
                    <button
                        type="submit"
                        id="signout"
                    ></button>
                </form>
            </div>
        </li>
    </ul>
</nav>
