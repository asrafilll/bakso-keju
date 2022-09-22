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
                        href="{{ url('/purchases') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ $purchase->purchase_number }}</h1>
                </div>
                @if ($purchase->deleted_at)
                    <div class="col-auto">
                        <span class="badge badge-danger">{{ __('Deleted at') }}: {{ $purchase->deleted_at }}</span>
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
                                        <dd>{{ $purchase->branch->name }}</dd>
                                    </dl>
                                </div>
                                <div class="col">
                                    <dl>
                                        <dt>{{ __('Customer name') }}</dt>
                                        <dd>{{ $purchase->customer_name }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('items') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('item') }}</th>
                                            <th
                                                width="100px"
                                                class="text-right"
                                            >{{ __('Quantity') }}</th>
                                            <th width="250px"class="text-right">{{ __('Total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($purchase->purchaseLineItems as $purchaseLineItem)
                                            <tr>
                                                <td>
                                                    <div>{{ $purchaseLineItem->item_name }}</div>
                                                    <div>{{ $purchaseLineItem->idr_price }}</div>
                                                </td>
                                                <td class="text-right">{{ $purchaseLineItem->idr_quantity }}</td>
                                                <td class="text-right">{{ $purchaseLineItem->idr_total }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>{{ __('Sub Total') }}</th>
                                            <th class="text-right">{{ $purchase->idr_total_line_items_quantity }}</th>
                                            <th class="text-right">{{ $purchase->idr_total_line_items_price }}</th>
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
                                        <td>{{ __('Sub Total') }}</td>
                                        <td class="text-right">{{ $purchase->idr_total_line_items_price }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ __('Total') }}</td>
                                        <td class="text-right">{{ $purchase->idr_total_price }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <a
                        href="{{ Request::fullUrlWithQuery(['action' => 'print-invoice']) }}"
                        class="btn btn-primary"
                        target="_blank"
                    >{{ __('Print invoice') }}</a>
                    @if (is_null($purchase->deleted_at))
                        <button
                            type="button"
                            class="btn btn-danger"
                            data-toggle="modal"
                            data-target="#modal-delete"
                            data-action="{{ url('/purchases/' . $purchase->id) }}"
                        >{{ __('Delete') }}</button>
                    @endif
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
