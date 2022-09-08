<nav class="main-header navbar navbar-expand navbar-white navbar-light">
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
                    src="https://via.placeholder.com/50"
                    class="img-circle"
                    alt="User"
                    height="100%"
                />
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item">{{ Auth::user()->name }}</span>
                <div class="dropdown-divider"></div>
                <a
                    href="#"
                    class="dropdown-item"
                >Your profile</a>
                <a
                    href="#"
                    class="dropdown-item"
                >Change Password</a>
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
                    <button type="submit" id="signout"></button>
                </form>
            </div>
        </li>
    </ul>
</nav>
