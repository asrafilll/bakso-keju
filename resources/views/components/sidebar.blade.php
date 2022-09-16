<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <a
        href="index3.html"
        class="brand-link"
    >
        <img
            src="https://via.placeholder.com/60"
            class="brand-image img-circle"
            style="opacity: .8"
        >
        <span class="brand-text font-weight-light">{{ Config::get('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul
                class="nav nav-pills nav-sidebar flex-column nav-legacy"
                data-widget="treeview"
                role="menu"
                data-accordion="false"
            >
                <x-nav-item :href="url('/')" activeHref="/">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </x-nav-item>
                <x-nav-item :href="url('/order-sources')" activeHref="order-sources/*">
                    <i class="nav-icon fas fa-network-wired"></i>
                    <p>{{ __('Order Sources') }}</p>
                </x-nav-item>
                <x-nav-item :href="url('/products')" activeHref="products/*">
                    <i class="nav-icon fas fa-box"></i>
                    <p>{{ __('Products') }}</p>
                </x-nav-item>
                <x-nav-item :href="url('/product-categories')" activeHref="product-categories/*">
                    <i class="nav-icon fas fa-boxes"></i>
                    <p>{{ __('Product Categories') }}</p>
                </x-nav-item>
                <x-nav-item :href="url('/branches')" activeHref="branches/*">
                    <i class="nav-icon fas fa-network-wired"></i>
                    <p>{{ __('Branches') }}</p>
                </x-nav-item>
                <x-nav-item :href="url('/users')" activeHref="users/*">
                    <i class="nav-icon fas fa-users"></i>
                    <p>{{ __('Users') }}</p>
                </x-nav-item>
                <x-nav-item :href="url('/roles')" activeHref="roles/*">
                    <i class="nav-icon fas fa-lock"></i>
                    <p>{{ __('Roles') }}</p>
                </x-nav-item>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
