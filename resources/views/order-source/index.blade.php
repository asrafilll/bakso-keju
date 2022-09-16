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
                    <h1 class="m-0">{{ __('Order Sources') }}</h1>
                </div><!-- /.col -->
                <div class="col-auto">
                    <a
                        href="{{ url('/order-sources/create') }}"
                        class="btn btn-primary"
                    >{{ __('Create order source') }}</a>
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
                                                placeholder="{{ __('Filter order source') }}"
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
                                    @forelse ($orderSources as $orderSource)
                                        <tr>
                                            <td class="align-middle">{{ $orderSource->name }}</td>
                                            <td class="align-middle">{{ $orderSource->created_at }}</td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-sm">
                                                    <a
                                                        href="{{ url('/order-sources/' . $orderSource->id) }}"
                                                        class="btn btn-default"
                                                    >{{ __('Detail') }}</a>
                                                    <button
                                                        type="button"
                                                        class="btn btn-danger"
                                                        data-toggle="modal"
                                                        data-target="#modal-delete"
                                                        data-action="{{ url('/order-sources/' . $orderSource->id) }}"
                                                    >{{ __('Delete') }}</button>
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
                            {!! $orderSources->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
