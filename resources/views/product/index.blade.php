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
        <div class="container">
            <div class="row justify-content-between mb-2">
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Products') }}</h1>
                </div><!-- /.col -->
                <div class="col-auto">
                    <a
                        href="{{ url('/products/create') }}"
                        class="btn btn-primary"
                    >{{ __('Create product') }}</a>
                </div><!-- /.col -->
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
                                                placeholder="{{ __('Filter products') }}"
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
                                                    'sort' => 'name',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'name' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Name ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'name',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'name' && Request::get('direction') == 'desc' ? 'active' : '' }}"
                                            >{{ __('Name descending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'price',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'price' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Price ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'price',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'price' && Request::get('direction') == 'desc' ? 'active' : '' }}"
                                            >{{ __('Price descending') }}</a>
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Date created') }}</th>
                                        <th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td class="align-middle">{{ $product->name }}</td>
                                            <td class="align-middle">{{ $product->idr_price }}</td>
                                            <td class="align-middle">{{ $product->created_at }}</td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-sm">
                                                    <a
                                                        href="{{ url('/products/' . $product->id) }}"
                                                        class="btn btn-default"
                                                    >{{ __('Detail') }}</a>
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger"
                                                        data-toggle="modal"
                                                        data-target="#modal-delete"
                                                        data-action="{{ url('/products/' . $product->id) }}"
                                                    >{{ __('Delete') }}</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                colspan="4"
                                                class="text-center"
                                            >{{ __('Data not found') }}</td>
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
