<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8CAYAAAA6/NlyAAAAAXNSR0IArs4c6QAAAnlJREFUaEPtmUmLIkEQhcN9RwT3dkNwQdz+/2/w4EXcULvdUQRFcUFth5eDTqkzTdtWHzon4lSYWRn14nsReVBTKpVO9B+FhgVLTpsJSw6YmDATlqwCbGnJgN7JYcJMWLIKsKUlA8pDiy3NlpasAmxpyYDylGZLs6UVFdBoNBSJRMjlctHxeKThcEiz2UzscDqdFA6HyWAw0Gq1otfXV9rv91+q33fmecjSwWCQzGazEGOxWCgajVKtViN8YDabpU6nQ8vlkl5eXshoNFK73f6S4O/M85DgXC5HzWaTttvtlRAQd7vdYg2h0+kon89TuVym0+nPHxs+n4/sdju1Wi2xL5FI0Hw+p+l0enXes3k+qvKnBUNEoVCgfr9PXq+X3t/fhaXxwYFAgPR6PfV6vUsuCK7X67Tb7S6/wQnpdJoGgwFptVry+/3CIcpQI48qgmFR2BYix+Mx2Ww2QahSqYgCQAyKcQ5QAsn1en2V32q1UiwWE/th+c1mc7WuVp5/iX6IcLFYFDbFwEIkk0lhR5PJJHq22+1e8mBvtVq9InxeTKVS4hEOuA0QVivP30R/WjBevhUBwZPJRNDyeDzUaDREDkxquOG2h8/THFbGO6PRiBaLxd13qZHnacI4ANcOCLy9vRGsebY0+lk5pc/7MLWVgb7NZDLC6niOx+OiJfC+Mp7No0oP4xCIRf85HA46HA6iZzG0EPgN1xTo4mrC1YU9ygiFQoLsebhhP8Qqh50aeVQT/NFBP2XtoR7+KaKYsKICTFgG27Kl2dKy+5gJM2F5K8D3sLxsfytjwkxYsgqwpSUDeieHCTNhySrAlpYMKA8ttjRbWrIK/AIA6DmP8pRfKAAAAABJRU5ErkJggg==" class="brand-image img-circle" style="opacity: .8">
        <span class="brand-text font-weight-light">{{ Config::get('app.name') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-legacy" data-widget="treeview" role="menu"
                data-accordion="false">
                <x-nav-item :href="url('/')" activeHref="/">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </x-nav-item>
                @canany([\App\Enums\PermissionEnum::view_orders()->value,
                    \App\Enums\PermissionEnum::view_inventories()->value,
                    \App\Enums\PermissionEnum::view_product_inventories()->value,
                    \App\Enums\PermissionEnum::view_products()->value,
                    \App\Enums\PermissionEnum::view_product_categories()->value,
                    \App\Enums\PermissionEnum::view_order_sources()->value,
                    \App\Enums\PermissionEnum::view_resellers()->value])
                    <li class="nav-header">{{ __('Sales') }}</li>
                @endcanany
                @can(\App\Enums\PermissionEnum::view_orders()->value)
                    <x-nav-item :href="url('/orders')" activeHref="orders">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>{{ __('Orders') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_inventories()->value)
                    <x-nav-item :href="url('/inventories')" activeHref="inventories">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>{{ __('Inventories') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_product_inventories()->value)
                    <x-nav-item :href="url('/product-inventories')" activeHref="product-inventories">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>{{ __('Product Inventories') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_products()->value)
                    <x-nav-item :href="url('/products')" activeHref="products">
                        <i class="nav-icon fas fa-box"></i>
                        <p>{{ __('Products') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_product_categories()->value)
                    <x-nav-item :href="url('/product-categories')" activeHref="product-categories">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>{{ __('Product Categories') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_resellers()->value)
                    <x-nav-item :href="url('/resellers')" activeHref="resellers">
                        <i class="nav-icon fas fa-user-tag"></i>
                        <p>{{ __('Resellers') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_order_sources()->value)
                    <x-nav-item :href="url('/order-sources')" activeHref="order-sources">
                        <i class="nav-icon fas fa-truck-loading"></i>
                        <p>{{ __('Order Sources') }}</p>
                    </x-nav-item>
                @endcan
                @canany([\App\Enums\PermissionEnum::view_purchases()->value,
                    \App\Enums\PermissionEnum::view_item_inventories()->value,
                    \App\Enums\PermissionEnum::view_items()->value,
                    \App\Enums\PermissionEnum::view_item_categories()->value])
                    <li class="nav-header">{{ __('Purchasing') }}</li>
                @endcanany
                @can(\App\Enums\PermissionEnum::view_purchases()->value)
                    <x-nav-item :href="url('/purchases')" activeHref="purchases">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>{{ __('Purchases') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_item_inventory_histories()->value)
                    <x-nav-item :href="url('/item-inventory-histories')" activeHref="item-inventory-histories">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>{{ __('Inventories') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_item_inventories()->value)
                    <x-nav-item :href="url('/item-inventories')" activeHref="item-inventories">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>{{ __('Item Inventories') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_items()->value)
                    <x-nav-item :href="url('/items')" activeHref="items">
                        <i class="nav-icon fas fa-box"></i>
                        <p>{{ __('Items') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_item_categories()->value)
                    <x-nav-item :href="url('/item-categories')" activeHref="item-categories">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>{{ __('Item Categories') }}</p>
                    </x-nav-item>
                @endcan
                @canany([\App\Enums\PermissionEnum::view_manufacture_products()->value,
                    \App\Enums\PermissionEnum::view_manufacture_product_components()->value,
                    \App\Enums\PermissionEnum::view_product_component_inventories()->value,
                    \App\Enums\PermissionEnum::view_product_components()->value])
                    <li class="nav-header">{{ __('Manufacture') }}</li>
                @endcanany
                @can(\App\Enums\PermissionEnum::view_manufacture_products()->value)
                    <x-nav-item :href="url('/manufacture-products')" activeHref="manufacture-products">
                        <i class="nav-icon fas fa-sync"></i>
                        <p>{{ __('Manufacture Products') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_manufacture_product_components()->value)
                    <x-nav-item :href="url('/manufacture-product-components')" activeHref="manufacture-product-components">
                        <i class="nav-icon fas fa-sync"></i>
                        <p>{{ __('Manufacture Components') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_component_inventories()->value)
                    <x-nav-item :href="url('/component-inventories')" activeHref="component-inventories">
                        <i class="nav-icon fas fa-exchange-alt"></i>
                        <p>{{ __('Inventories') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_product_components()->value)
                    <x-nav-item :href="url('/product-component-inventories')" activeHref="product-component-inventories">
                        <i class="nav-icon fas fa-box-open"></i>
                        <p>{{ __('Component Inventories') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_product_components()->value)
                    <x-nav-item :href="url('/product-components')" activeHref="product-components">
                        <i class="nav-icon fas fa-toolbox"></i>
                        <p>{{ __('Components') }}</p>
                    </x-nav-item>
                @endcan
                @canany([\App\Enums\PermissionEnum::view_branches()->value])
                    <li class="nav-header">{{ __('Master') }}</li>
                @endcanany
                @can(\App\Enums\PermissionEnum::view_branches()->value)
                    <x-nav-item :href="url('/branches')" activeHref="branches">
                        <i class="nav-icon fas fa-network-wired"></i>
                        <p>{{ __('Branches') }}</p>
                    </x-nav-item>
                @endcan
                @canany([\App\Enums\PermissionEnum::view_users()->value,
                    \App\Enums\PermissionEnum::view_roles()->value])
                    <li class="nav-header">{{ __('Security') }}</li>
                @endcanany
                @can(\App\Enums\PermissionEnum::view_users()->value)
                    <x-nav-item :href="url('/users')" activeHref="users">
                        <i class="nav-icon fas fa-users"></i>
                        <p>{{ __('Users') }}</p>
                    </x-nav-item>
                @endcan
                @can(\App\Enums\PermissionEnum::view_roles()->value)
                    <x-nav-item :href="url('/roles')" activeHref="roles">
                        <i class="nav-icon fas fa-lock"></i>
                        <p>{{ __('Roles') }}</p>
                    </x-nav-item>
                @endcan
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
