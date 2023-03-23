<x-app>
    @if (Session::has('success'))
        <script>
            toastr.success('{{ Session::get('success') }}');
        </script>
    @endif
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row align-items-center mb-2">
                <div class="col-auto">
                    <a href="{{ url('/orders') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ $order->order_number }}</h1>
                </div>
                @if ($order->deleted_at)
                    <div class="col-auto">
                        <span class="badge badge-danger">{{ __('Deleted at') }}: {{ $order->deleted_at }}</span>
                    </div>
                @endif
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <dl>
                                        <dt>{{ __('Branch') }}</dt>
                                        <dd>{{ $order->branch->name }}</dd>
                                    </dl>
                                </div>
                                <div class="col">
                                    <dl>
                                        <dt>{{ __('Order source') }}</dt>
                                        <dd>{{ $order->orderSource->name }}</dd>
                                    </dl>
                                </div>
                                <div class="col">
                                    <dl>
                                        <dt>{{ __('Customer name') }}</dt>
                                        <dd>{{ $order->customer_name }}</dd>
                                    </dl>
                                </div>
                                <div class="col">
                                    <dl>
                                        <dt>{{ __('Customer phone number') }}</dt>
                                        <dd>{{ $order->customer_phone_number ?? '-' }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Products') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Product') }}</th>
                                            <th width="100px" class="text-right">{{ __('Quantity') }}</th>
                                            <th width="250px"class="text-right">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderLineItems as $orderLineItem)
                                            <tr>
                                                <td>
                                                    <div>{{ $orderLineItem->product_name }}</div>
                                                    <div>{{ $orderLineItem->idr_price }}</div>
                                                </td>
                                                <td class="text-right">{{ $orderLineItem->idr_quantity }}</td>
                                                <td class="text-right">{{ $orderLineItem->idr_total }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <thead>
                                        <tr>
                                            <th>{{ __('Product bundle') }}</th>
                                            <th width="100px" class="text-right">{{ __('Quantity') }}</th>
                                            <th width="250px"class="text-right">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderLineHampers as $orderLineHamper)
                                            <tr>
                                                <td>
                                                    <div>{{ $orderLineHamper->hamper_name }}</div>
                                                    <div>{{ $orderLineHamper->idr_price }}</div>
                                                </td>
                                                <td class="text-right">
                                                    @foreach ($orderLineHamper->productHamper->productHamperLines as $item)
                                                        <div>
                                                            {{ $item->product->name }}
                                                            ({{ $item->quantity }})
                                                        </div>
                                                    @endforeach
                                                </td>
                                                <td class="text-right">{{ $orderLineHamper->idr_total }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>{{ __('Sub Total') }}</th>
                                            <th class="text-right">{{ $order->idr_total_line_items_quantity }}</th>
                                            <th class="text-right">{{ $order->idr_total_line_items_price }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Summary') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr>
                                        <td>{{ __('Percentage Discount') }}</td>
                                        <td class="text-right">{{ $order->percentage_discount }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Total Discount') }}</td>
                                        <td class="text-right">{{ $order->idr_total_discount }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Sub Total') }}</td>
                                        <td class="text-right">{{ $order->idr_total_line_items_price }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Total') }}</td>
                                        <td class="text-right">{{ $order->idr_total_price }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <a href="{{ Request::fullUrlWithQuery(['action' => 'print-invoice']) }}" class="btn btn-primary"
                        target="_blank">{{ __('Print invoice') }}</a>
                    @can(\App\Enums\PermissionEnum::delete_order()->value)
                        @if (is_null($order->deleted_at))
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-delete"
                                data-action="{{ url('/orders/' . $order->id) }}">{{ __('Delete') }}</button>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
