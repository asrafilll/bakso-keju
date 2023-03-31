<x-app>
    @if (Session::has('success'))
        <script>
            toastr.success('{{ Session::get('success') }}');
        </script>
    @endif
    @if (Session::has('error'))
        <script>
            toastr.error('{{ Session::get('error') }}');
        </script>
    @endif

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row justify-content-between mb-2">
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Product bundle') }}</h1>
                </div><!-- /.col -->
                @can(\App\Enums\PermissionEnum::view_product_hampers()->value)
                    <div class="col-auto">
                        <a href="{{ url('/product-hampers/create') }}" class="btn btn-primary">{{ __('Create bundle') }}</a>
                    </div><!-- /.col -->
                @endcan
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
                        <div class="card-header">
                            <div class="row">
                                <div class="col">
                                    <form action="" method="GET">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                            <input type="search" name="term" class="form-control"
                                                value="{{ Request::get('term') }}"
                                                placeholder="{{ __('Search bundles') }}" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Products') }}</th>
                                        <th>{{ __('Charge') }}</th>
                                        <th>{{ __('Date created') }}</th>
                                        <th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td class="align-middle">{{ $product->name }}</td>
                                            <td class="align-middle">
                                                <ul>
                                                    @foreach ($product->productHamperLines as $item)
                                                        <li>{{ $item->product->name }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td class="align-middle">{{ $product->idr_price }}</td>
                                            <td class="align-middle">{{ $product->created_at }}</td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-sm">
                                                    @can(\App\Enums\PermissionEnum::view_order_sources()->value)
                                                        <a href="{{ url('/product-hampers/' . $product->id) }}"
                                                            class="btn btn-default">{{ __('Detail') }}</a>
                                                    @endcan
                                                    @can(\App\Enums\PermissionEnum::view_product_hampers()->value)
                                                        <button type="button" class="btn btn-danger" data-toggle="modal"
                                                            data-target="#modal-delete"
                                                            data-action="{{ url('/product-hampers/' . $product->id) }}">{{ __('Delete') }}</button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">{{ __('Data not found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex justify-content-center">
                            {!! $products->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
