<x-app>
    @if (Session::has('success'))
        <script>
            toastr.success('{{ Session::get('success') }}');
        </script>
    @endif
    @if (Session::has('failed'))
        <script>
            toastr.error('{{ Session::get('failed') }}');
        </script>
    @endif

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row justify-content-between mb-2">
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Products') }}</h1>
                </div><!-- /.col -->
                @can(\App\Enums\PermissionEnum::create_product()->value)
                    <div class="col-auto ml-auto">
                        <a
                            href="{{ url('/products/import') }}"
                            class="btn btn-default"
                        >{{ __('Import') }}</a>
                    </div><!-- /.col -->
                    <div class="col-auto">
                        <a
                            href="{{ url('/products/create') }}"
                            class="btn btn-primary"
                        >{{ __('Create product') }}</a>
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
                                                placeholder="{{ __('Filter products') }}"
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
                                                                <span>{{ __('Filter products') }}</span>
                                                            </label>
                                                            <input
                                                                type="search"
                                                                name="term"
                                                                class="form-control"
                                                                value="{{ Request::get('term') }}"
                                                            />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="product_category_id">
                                                                <span>{{ __('Category') }}</span>
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <select
                                                                name="product_category_id"
                                                                id="product_category_id"
                                                                class="form-control @error('product_category_id') is-invalid @enderror"
                                                            >
                                                                <option
                                                                    value=""
                                                                    hidden
                                                                ></option>
                                                                @foreach ($productCategories as $productCategory)
                                                                    <option
                                                                        value="{{ $productCategory->id }}"
                                                                        @if (Request::old('product_category_id') == $productCategory->id) selected @endif
                                                                    >{{ $productCategory->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('product_category_id')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
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
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Price') }}</th>
                                        <th>{{ __('Date created') }}</th>
                                        <th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td class="align-middle">{{ $product->name }}</td>
                                            <td class="align-middle">{{ $product->productCategory->name }}</td>
                                            <td class="align-middle">{{ $product->idr_price }}</td>
                                            <td class="align-middle">{{ $product->created_at }}</td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-sm">
                                                    @can(\App\Enums\PermissionEnum::view_products()->value)
                                                        <a
                                                            href="{{ url('/products/' . $product->id) }}"
                                                            class="btn btn-default"
                                                        >{{ __('Detail') }}</a>
                                                    @endcan
                                                    @can(\App\Enums\PermissionEnum::delete_product()->value)
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger"
                                                            data-toggle="modal"
                                                            data-target="#modal-delete"
                                                            data-action="{{ url('/products/' . $product->id) }}"
                                                        >{{ __('Delete') }}</button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                colspan="5"
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
