<x-app>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row justify-content-between mb-2">
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Product components') }}</h1>
                </div><!-- /.col -->
                @can(\App\Enums\PermissionEnum::create_product_component()->value)
                    <div class="col-auto">
                        <a
                            href="{{ url('/product-components/create') }}"
                            class="btn btn-primary"
                        >{{ __('Create product component') }}</a>
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
                                                name="filter"
                                                class="form-control"
                                                value="{{ Request::get('filter') }}"
                                                placeholder="{{ __('Filter product components') }}"
                                            />
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
                                        <th>{{ __('Date created') }}</th>
                                        <th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($productComponents as $productComponent)
                                        <tr>
                                            <td class="align-middle">{{ $productComponent->name }}</td>
                                            <td class="align-middle">{{ $productComponent->created_at }}</td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-sm">
                                                    @can(\App\Enums\PermissionEnum::view_product_components()->value)
                                                        <a
                                                            href="{{ url('/product-components/' . $productComponent->id) }}"
                                                            class="btn btn-default"
                                                        >{{ __('Detail') }}</a>
                                                    @endcan
                                                    @can(\App\Enums\PermissionEnum::delete_product_component()->value)
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger"
                                                            data-toggle="modal"
                                                            data-target="#modal-delete"
                                                            data-action="{{ url('/product-components/' . $productComponent->id) }}"
                                                        >{{ __('Delete') }}</button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                colspan="3"
                                                class="text-center"
                                            >{{ __('Data not found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex justify-content-center">
                            {!! $productComponents->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
