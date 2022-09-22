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
                                                name="term"
                                                class="form-control"
                                                value="{{ Request::get('term') }}"
                                                placeholder="{{ __('Filter orders') }}"
                                            />
                                        </div>
                                    </form>
                                </div>
                                <div class="col-auto">
                                    <button
                                        type="button"
                                        class="btn btn-default"
                                        data-toggle="modal"
                                        data-target="#filterModal"
                                    >
                                        <i class="fas fa-filter"></i>
                                        <span>{{ __('Filter') }}</span>
                                    </button>
                                    <div
                                        class="modal fade"
                                        id="filterModal"
                                        tabindex="-1"
                                        aria-labelledby="filterModalLabel"
                                        aria-hidden="true"
                                    >
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form
                                                    action=""
                                                    method="GET"
                                                >
                                                    <div class="modal-header">
                                                        <h5
                                                            class="modal-title"
                                                            id="filterModalLabel"
                                                        >{{ __('Filter') }}</h5>
                                                        <button
                                                            type="button"
                                                            class="close"
                                                            data-dismiss="modal"
                                                            aria-label="Close"
                                                        >
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="term">
                                                                <span>{{ __('Filter orders') }}</span>
                                                            </label>
                                                            <input
                                                                type="search"
                                                                name="term"
                                                                class="form-control"
                                                                value="{{ Request::get('term') }}"
                                                            />
                                                        </div>
                                                        <div
                                                            class="form-group"
                                                            id="branch-module"
                                                        >
                                                            <label for="branch_id">
                                                                <span>{{ __('Branch') }}</span>
                                                            </label>
                                                            <select
                                                                id="branch_id"
                                                                name="branch_id"
                                                                class="form-control @error('branch_id') is-invalid @enderror"
                                                                style="width: 100%;"
                                                            >
                                                                <option></option>
                                                                @if (Request::get('branch_id') && Request::get('branch_name'))
                                                                    <option
                                                                        value="{{ Request::get('branch_id') }}"
                                                                        selected
                                                                    >{{ Request::get('branch_name') }}</option>
                                                                @endif
                                                            </select>
                                                            <input
                                                                type="hidden"
                                                                id="branch_name"
                                                                name="branch_name"
                                                                value="{{ Request::get('branch_name') }}"
                                                            />
                                                            @error('branch_id')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <script>
                                                            var BranchModule = (function() {
                                                                var $el = $('#branch-module');
                                                                var $branchId = $el.find('#branch_id');
                                                                var $branchName = $el.find('#branch_name');

                                                                $branchId.on('select2:select', function(e) {
                                                                    $branchName.val(e.params.data.text);
                                                                });

                                                                $branchId.on('select2:unselect', function() {
                                                                    $branchName.val(null);
                                                                });

                                                                $branchId.on('select2:clear', function() {
                                                                    setTimeout(function () {
                                                                        $branchId.select2('close');
                                                                    }, 0);
                                                                });

                                                                function init() {
                                                                    $branchId.select2({
                                                                        theme: 'bootstrap4',
                                                                        placeholder: '',
                                                                        allowClear: true,
                                                                        ajax: {
                                                                            url: '/orders?action=fetch-branches',
                                                                            dataType: 'json',
                                                                            delay: 250,
                                                                            processResults: function(branches) {
                                                                                return {
                                                                                    results: branches.map(function(branch) {
                                                                                        return {
                                                                                            id: branch.id,
                                                                                            text: branch.name,
                                                                                            branch: branch,
                                                                                        };
                                                                                    }),
                                                                                };
                                                                            },
                                                                        },
                                                                    });
                                                                }

                                                                init();
                                                            })()
                                                        </script>
                                                        <div
                                                            class="form-group"
                                                            id="order-source-module"
                                                        >
                                                            <label for="order_source_id">
                                                                <span>{{ __('Order source') }}</span>
                                                            </label>
                                                            <select
                                                                id="order_source_id"
                                                                name="order_source_id"
                                                                class="form-control @error('order_source_id') is-invalid @enderror"
                                                                style="width: 100%;"
                                                            >
                                                                <option></option>
                                                                @if (Request::get('order_source_id') && Request::get('order_source_name'))
                                                                    <option
                                                                        value="{{ Request::get('order_source_id') }}"
                                                                        selected
                                                                    >{{ Request::get('order_source_name') }}</option>
                                                                @endif
                                                            </select>
                                                            <input
                                                                type="hidden"
                                                                id="order_source_name"
                                                                name="order_source_name"
                                                                value="{{ Request::get('order_source_name') }}"
                                                            />
                                                            @error('order_source_id')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <script>
                                                            var OrderSourceModule = (function() {
                                                                var $el = $('#order-source-module');
                                                                var $orderSourceId = $el.find('#order_source_id');
                                                                var $orderSourceName = $el.find('#order_source_name');

                                                                $orderSourceId.on('select2:select', function(e) {
                                                                    $orderSourceName.val(e.params.data.text);
                                                                });

                                                                $orderSourceId.on('select2:unselect', function() {
                                                                    $orderSourceName.val(null);
                                                                });

                                                                $orderSourceId.on('select2:clear', function() {
                                                                    setTimeout(function () {
                                                                        $orderSourceId.select2('close');
                                                                    }, 0);
                                                                });

                                                                function init() {
                                                                    $orderSourceId.select2({
                                                                        theme: 'bootstrap4',
                                                                        placeholder: '',
                                                                        allowClear: true,
                                                                        ajax: {
                                                                            url: '/orders?action=fetch-order-sources',
                                                                            dataType: 'json',
                                                                            delay: 250,
                                                                            processResults: function(orderSources) {
                                                                                return {
                                                                                    results: orderSources.map(function(orderSource) {
                                                                                        return {
                                                                                            id: orderSource.id,
                                                                                            text: orderSource.name,
                                                                                        };
                                                                                    }),
                                                                                };
                                                                            },
                                                                        },
                                                                    });
                                                                }

                                                                init();
                                                            })();
                                                        </script>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button
                                                            type="button"
                                                            class="btn btn-default"
                                                            data-dismiss="modal"
                                                        >{{ __('Close') }}</button>
                                                        <button
                                                            type="submit"
                                                            class="btn btn-primary"
                                                        >{{ __('Save') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
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
                            <table class="table table-hover text-nowrap">
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
                                                @if ($order->deleted_at)
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
