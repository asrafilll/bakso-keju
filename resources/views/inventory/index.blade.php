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
                    <h1 class="m-0">{{ __('Inventories') }}</h1>
                </div><!-- /.col -->
                <div class="col-auto">
                    <a
                        href="{{ url('/inventories/create') }}"
                        class="btn btn-primary"
                    >{{ __('Create inventory') }}</a>
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
                                                placeholder="{{ __('Filter inventories') }}"
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
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Branch') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Date created') }}</th>
                                        <th>{{ __('Date updated') }}</th>
                                        <th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($inventories as $inventory)
                                        <tr>
                                            <td class="align-middle">{{ $inventory->product_name }}</td>
                                            <td class="align-middle">{{ $inventory->branch_name }}</td>
                                            <td class="align-middle">{{ $inventory->quantity }}</td>
                                            <td class="align-middle">{{ $inventory->created_at }}</td>
                                            <td class="align-middle">{{ $inventory->updated_at }}</td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-sm">
                                                    <a
                                                        href="{{ url('/inventories/' . $inventory->id) }}"
                                                        class="btn btn-default"
                                                    >{{ __('Detail') }}</a>
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger"
                                                        data-toggle="modal"
                                                        data-target="#modal-delete"
                                                        data-action="{{ url('/inventories/' . $inventory->id) }}"
                                                    >{{ __('Delete') }}</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td
                                                colspan="6"
                                                class="text-center"
                                            >{{ __('Data not found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer d-flex justify-content-center">
                            {!! $inventories->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
