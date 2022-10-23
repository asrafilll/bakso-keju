<x-app>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row justify-content-between mb-2">
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Dashboard') }}</h1>
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
                        <div class="card-body">
                            <form
                                action="{{ '/dashboard' }}"
                                method="GET"
                            >
                                <div class="form-row align-items-center">
                                    <div class="form-group row col-sm-4">
                                        <label
                                            for="from_date"
                                            class="col-form-label col-4 col-sm-auto text-right"
                                        >{{ __('From date') }}</label>
                                        <div class="col">
                                            <input
                                                type="date"
                                                name="from_date"
                                                id="from_date"
                                                class="form-control"
                                                value="{{ Request::get('from_date', \Illuminate\Support\Carbon::now()->format('Y-m-d')) }}"
                                                onchange="document.getElementById('to_date').setAttribute('min', this.value)"
                                            />
                                        </div>
                                    </div>
                                    <div class="form-group row col-sm-4">
                                        <label
                                            for="to_date"
                                            class="col-form-label col-4 col-sm-auto text-right"
                                        >{{ __('To date') }}</label>
                                        <div class="col">
                                            <input
                                                type="date"
                                                name="to_date"
                                                id="to_date"
                                                class="form-control"
                                                value="{{ Request::get('to_date', \Illuminate\Support\Carbon::now()->format('Y-m-d')) }}"
                                                onchange="document.getElementById('from_date').setAttribute('max', this.value)"
                                            />
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-auto text-right">
                                        <button
                                            type="submit"
                                            class="btn btn-primary"
                                        >{{ __('Filter') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th
                                            rowspan="3"
                                            class="align-middle text-center"
                                        >{{ __('No') }}</th>
                                        <th
                                            rowspan="3"
                                            class="align-middle"
                                        >{{ __('Product') }}</th>
                                        <th
                                            rowspan="3"
                                            class="align-middle text-center"
                                        >{{ __('In Pcs') }}</th>
                                        <th
                                            rowspan="3"
                                            class="align-middle text-center"
                                        >{{ __('In Value') }}</th>
                                        @foreach ($branches as $branch)
                                            <th
                                                colspan="{{ count($branch['order_sources']) * 2 }}"
                                                class="text-center"
                                            >{{ $branch['name'] }}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($branches as $branch)
                                            @foreach ($branch['order_sources'] as $orderSource)
                                                <th
                                                    colspan="2"
                                                    class="text-center"
                                                >{{ $orderSource['name'] }}</th>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($branches as $branch)
                                            @foreach ($branch['order_sources'] as $orderSource)
                                                <th>{{ __('In Pcs') }}</th>
                                                <th>{{ __('In Value') }}</th>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td class="text-right">{{ $loop->iteration }}</td>
                                            <td>{{ $product['product_name'] }}</td>
                                            <td class="text-right">{{ $product['idr_total_quantity'] }}</td>
                                            <td class="text-right">{{ $product['idr_total_price'] }}</td>
                                            @foreach ($product['branches'] as $branch)
                                                @foreach ($branch['order_sources'] as $orderSource)
                                                    <td class="text-right">{{ $orderSource['idr_total_quantity'] }}
                                                    </td>
                                                    <td class="text-right">{{ $orderSource['idr_total_price'] }}</td>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
