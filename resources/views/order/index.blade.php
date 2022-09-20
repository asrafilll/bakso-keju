<x-app>
    @if (Session::has('success'))
        <script>
            toastr.success('{{ Session::get('success') }}');
        </script>
    @endif

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="d-flex mb-2">
        </div>
        <div class="container-fluid">
            <div class="row justify-content-between mb-2">
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Orders') }}</h1>
                </div><!-- /.col -->
                <div class="col-auto">
                    <a
                        href="{{ url('/orders/create') }}"
                        class="btn btn-primary"
                    >{{ __('Create order') }}</a>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <form
                                        action=""
                                        method="GET"
                                    >
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                            <input
                                                type="search"
                                                name="filter"
                                                class="form-control"
                                                value="{{ Request::get('filter') }}"
                                                placeholder="{{ __('Filter orders') }}"
                                            />
                                        </div>
                                    </form>
                                </div>
                                <div class="col-auto">
                                    <div class="dropdown">
                                        <button
                                            type="button"
                                            class="btn btn-default"
                                            data-toggle="dropdown"
                                        >
                                            <i class="fas fa-sort"></i>
                                            <span>{{ __('Sort') }}</span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'order_number',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'order_number' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Order number ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'order_number',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'order_number' && Request::get('direction') == 'desc' ? 'active' : '' }}"
                                            >{{ __('Order number descending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'created_at',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'created_at' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Date created ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'created_at',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ (Request::get('sort') == 'created_at' && Request::get('direction') == 'desc') || (!Request::filled('sort') && !Request::filled('direction')) ? 'active' : '' }}"
                                            >{{ __('Date created descending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'percentage_discount',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'percentage_discount' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Percentage discount ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'percentage_discount',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'percentage_discount' && Request::get('direction') == 'desc' ? 'active' : '' }}"
                                            >{{ __('Percentage discount descending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'total_discount',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'total_discount' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Total discount ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'total_discount',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'total_discount' && Request::get('direction') == 'desc' ? 'active' : '' }}"
                                            >{{ __('Total discount descending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'total_line_items_quantity',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'total_line_items_quantity' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Total line items quantity ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'total_line_items_quantity',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'total_line_items_quantity' && Request::get('direction') == 'desc' ? 'active' : '' }}"
                                            >{{ __('Total line items quantity descending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'total_line_items_price',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'total_line_items_price' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Total line items price ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'total_line_items_price',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'total_line_items_price' && Request::get('direction') == 'desc' ? 'active' : '' }}"
                                            >{{ __('Total line items price descending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'total_price',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'total_price' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Total price ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'total_price',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'total_price' && Request::get('direction') == 'desc' ? 'active' : '' }}"
                                            >{{ __('Total price descending') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Order number') }}</th>
                                        <th>{{ __('Date created') }}</th>
                                        <th>{{ __('Branch') }}</th>
                                        <th>{{ __('Order source') }}</th>
                                        <th>{{ __('Customer name') }}</th>
                                        <th>{{ __('Percentage discount') }}</th>
                                        <th>{{ __('Total discount') }}</th>
                                        <th>{{ __('Total line items quantity') }}</th>
                                        <th>{{ __('Total line items price') }}</th>
                                        <th>{{ __('Total price') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $order)
                                        <tr>
                                            <td class="align-middle">
                                                <a href="{{ url('/orders/' . $order->id) }}">
                                                    {{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td class="align-middle">{{ $order->created_at }}</td>
                                            <td class="align-middle">{{ $order->branch_name }}</td>
                                            <td class="align-middle">{{ $order->order_source_name }}</td>
                                            <td class="align-middle">{{ $order->customer_name }}</td>
                                            <td class="align-middle">{{ $order->percentage_discount }}</td>
                                            <td class="align-middle">{{ $order->idr_total_discount }}</td>
                                            <td class="align-middle">{{ $order->idr_total_line_items_quantity }}</td>
                                            <td class="align-middle">{{ $order->idr_total_line_items_price }}</td>
                                            <td class="align-middle">{{ $order->idr_total_price }}</td>
                                            <td class="align-middle">
                                                @if($order->deleted_at)
                                                    <span class="badge badge-danger">{{ __('Deleted') }}</span>
                                                @else
                                                    <span class="badge badge-primary">{{ __('Active') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                colspan="11"
                                                class="text-center"
                                            >{{ __('Data not found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex justify-content-center">
                            {!! $orders->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
