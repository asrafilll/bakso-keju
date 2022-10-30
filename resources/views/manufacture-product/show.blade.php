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
                        href="{{ url('/manufacture-products') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ $manufactureProduct->order_number }}</h1>
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
                                        <dd>{{ $manufactureProduct->branch->name }}</dd>
                                    </dl>
                                </div>
                                <div class="col">
                                    <dl>
                                        <dt>{{ __('Created by') }}</dt>
                                        <dd>{{ $manufactureProduct->creator->name }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Product components') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Product component') }}</th>
                                            <th
                                                width="100px"
                                                class="text-right"
                                            >{{ __('Quantity') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($manufactureProduct->lineProductComponents as $lineProductComponent)
                                            <tr>
                                                <td>{{ $lineProductComponent->product_component_name }}</td>
                                                <td class="text-right">
                                                    {{ $lineProductComponent->idr_quantity }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>{{ __('Total') }}</th>
                                            <th class="text-right">
                                                {{ $manufactureProduct->idr_total_line_product_components_quantity }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
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
                                            <th
                                                width="100px"
                                                class="text-right"
                                            >{{ __('Quantity') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($manufactureProduct->lineProducts as $lineProduct)
                                            <tr>
                                                <td>{{ $lineProduct->product_name }}</td>
                                                <td class="text-right">
                                                    {{ $lineProduct->idr_quantity }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>{{ __('Total') }}</th>
                                            <th class="text-right">
                                                {{ $manufactureProduct->idr_total_line_products_quantity }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <button
                        type="button"
                        class="btn btn-danger"
                        data-toggle="modal"
                        data-target="#modal-delete"
                        data-action="{{ url('/manufacture-products/' . $manufactureProduct->id) }}"
                    >{{ __('Delete') }}</button>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
