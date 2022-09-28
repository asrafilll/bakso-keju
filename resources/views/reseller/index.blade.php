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
                    <h1 class="m-0">{{ __('Resellers') }}</h1>
                </div><!-- /.col -->
                @can(\App\Enums\PermissionEnum::create_reseller()->value)
                    <div class="col-auto">
                        <a
                            href="{{ url('/resellers/create') }}"
                            class="btn btn-primary"
                        >{{ __('Create reseller') }}</a>
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
                                                placeholder="{{ __('Filter resellers') }}"
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
                                        <th>{{ __('Percentage Discount') }}</th>
                                        <th>{{ __('Date created') }}</th>
                                        <th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($resellers as $reseller)
                                        <tr>
                                            <td class="align-middle">{{ $reseller->name }}</td>
                                            <td class="align-middle">{{ $reseller->percentage_discount }}</td>
                                            <td class="align-middle">{{ $reseller->created_at }}</td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-sm">
                                                    @can(\App\Enums\PermissionEnum::view_resellers()->value)
                                                        <a
                                                            href="{{ url('/resellers/' . $reseller->id) }}"
                                                            class="btn btn-default"
                                                        >{{ __('Detail') }}</a>
                                                    @endcan
                                                    @can(\App\Enums\PermissionEnum::delete_reseller()->value)
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger"
                                                            data-toggle="modal"
                                                            data-target="#modal-delete"
                                                            data-action="{{ url('/resellers/' . $reseller->id) }}"
                                                        >{{ __('Delete') }}</button>
                                                    @endcan
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
                            {!! $resellers->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
