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
                    <a
                        href="{{ url('/manufacturing-orders') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ $manufacturingOrder->order_number }}</h1>
                </div>
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
                                        <dd>{{ $manufacturingOrder->branch->name }}</dd>
                                    </dl>
                                </div>
                                <div class="col">
                                    <dl>
                                        <dt>{{ __('Created by') }}</dt>
                                        <dd>{{ $manufacturingOrder->creator->name }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Items') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item') }}</th>
                                            <th
                                                width="200px"
                                                class="text-right"
                                            >{{ __('Price') }}</th>
                                            <th
                                                width="100px"
                                                class="text-right"
                                            >{{ __('Quantity') }}</th>
                                            <th width="150px"class="text-right">{{ __('Total weight') }} (gram)</th>
                                            <th width="250px"class="text-right">{{ __('Total price') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($manufacturingOrder->manufacturingOrderLineItems as $manufacturingOrderLineItem)
                                            <tr>
                                                <td>{{ $manufacturingOrderLineItem->product_component_name }}</td>
                                                <td class="text-right">
                                                    {{ $manufacturingOrderLineItem->idr_price }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $manufacturingOrderLineItem->idr_quantity }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $manufacturingOrderLineItem->idr_total_weight }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $manufacturingOrderLineItem->idr_total_price }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">{{ __('Sub Total') }}</th>
                                            <th class="text-right">
                                                {{ $manufacturingOrder->idr_total_line_items_quantity }}
                                            </th>
                                            <th class="text-right">
                                                {{ $manufacturingOrder->idr_total_line_items_weight }}
                                            </th>
                                            <th class="text-right">
                                                {{ $manufacturingOrder->idr_total_line_items_price }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="btn btn-danger"
                        data-toggle="modal"
                        data-target="#modal-delete"
                        data-action="{{ url('/manufacturing-orders/' . $manufacturingOrder->id) }}"
                    >{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
