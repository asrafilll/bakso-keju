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
                            <form action="{{ '/dashboard' }}" method="GET">
                                <div class="form-row align-items-center">
                                    <div class="form-group row col-sm-4">
                                        <label for="from_date"
                                            class="col-form-label col-4 col-sm-auto text-right">{{ __('From date') }}</label>
                                        <div class="col">
                                            <input type="date" name="from_date" id="from_date" class="form-control"
                                                value="{{ Request::get('from_date', \Illuminate\Support\Carbon::now()->format('Y-m-d')) }}"
                                                onchange="document.getElementById('to_date').setAttribute('min', this.value)" />
                                        </div>
                                    </div>
                                    <div class="form-group row col-sm-4">
                                        <label for="to_date"
                                            class="col-form-label col-4 col-sm-auto text-right">{{ __('To date') }}</label>
                                        <div class="col">
                                            <input type="date" name="to_date" id="to_date" class="form-control"
                                                value="{{ Request::get('to_date', \Illuminate\Support\Carbon::now()->format('Y-m-d')) }}"
                                                onchange="document.getElementById('from_date').setAttribute('max', this.value)" />
                                        </div>
                                    </div>
                                    <div class="form-group col-sm-auto text-right">
                                        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                                        <a href="{{ Request::fullUrlWithQuery([
                                            'action' => 'export',
                                        ]) }}"
                                            class="btn btn-secondary">
                                            <span>{{ __('Export') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Summary per Branch') }}</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="10">{{ __('No') }}</th>
                                        <th class="text-center">{{ __('Branch') }}</th>
                                        <th class="text-center">{{ __('In Pcs') }}</th>
                                        <th class="text-center">{{ __('In Value') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($summary['branches'] as $branch)
                                        <tr>
                                            <td class="text-right">{{ $loop->iteration }}</td>
                                            <td>{{ $branch['name'] }}</td>
                                            <td class="text-right">{{ $branch['idr_total_quantity'] }}</td>
                                            <td class="text-right">{{ $branch['idr_total_price'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">{{ __('Total') }}</th>
                                        <th class="text-right">{{ $summary['idr_total_quantity'] }}</th>
                                        <th class="text-right">{{ $summary['idr_total_price'] }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Summary per Product Category') }}</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th rowspan="3" class="align-middle text-center" width="10">
                                            {{ __('No') }}</th>
                                        <th rowspan="3" class="align-middle">{{ __('Product') }}</th>
                                        <th rowspan="3" class="align-middle text-center">{{ __('In Pcs') }}</th>
                                        <th rowspan="3" class="align-middle text-center">{{ __('In Value') }}</th>
                                        @foreach ($branches as $branch)
                                            <th colspan="{{ count($branch['order_sources']) * 2 }}"
                                                class="text-center">{{ $branch['name'] }}</th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($branches as $branch)
                                            @foreach ($branch['order_sources'] as $orderSource)
                                                <th colspan="2" class="text-center">{{ $orderSource['name'] }}</th>
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
                                    @foreach ($summaryPerProductCategory as $category)
                                        <tr>
                                            <td class="text-right">{{ $loop->iteration }}</td>
                                            <td>{{ $category['name'] }}</td>
                                            <td class="text-right">{{ $category['idr_total_quantity'] }}</td>
                                            <td class="text-right">{{ $category['idr_total_price'] }}</td>
                                            @foreach ($category['branches'] as $branch)
                                                @foreach ($branch['order_sources'] as $orderSource)
                                                    <td class="text-right">{{ $orderSource['idr_total_quantity'] }}
                                                    </td>
                                                    <td class="text-right">{{ $orderSource['idr_total_price'] }}</td>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">{{ __('Total') }}</th>
                                        <th class="text-right">{{ $summary['idr_total_quantity'] }}</th>
                                        <th class="text-right">{{ $summary['idr_total_price'] }}</th>
                                        @foreach ($summary['branches'] as $branch)
                                            @foreach ($branch['order_sources'] as $orderSource)
                                                <td class="text-right">
                                                    {{ $orderSource['idr_total_quantity'] }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $orderSource['idr_total_price'] }}
                                                </td>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Summary per Product') }}</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <x-table-product-summary :branches="$branches" :products="$products" :summary="$summary" />
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
