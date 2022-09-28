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
                    <h1 class="m-0">{{ __('Users') }}</h1>
                </div><!-- /.col -->
                @can(\App\Enums\PermissionEnum::create_user()->value)
                    <div class="col-auto">
                        <a
                            href="{{ url('/users/create') }}"
                            class="btn btn-primary"
                        >{{ __('Create user') }}</a>
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
                                                placeholder="{{ __('Filter users') }}"
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
                                                    'sort' => 'email',
                                                    'direction' => 'asc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'email' && Request::get('direction') == 'asc' ? 'active' : '' }}"
                                            >{{ __('Email ascending') }}</a>
                                            <a
                                                href="{{ Request::fullUrlWithQuery([
                                                    'sort' => 'email',
                                                    'direction' => 'desc',
                                                ]) }}"
                                                class="dropdown-item {{ Request::get('sort') == 'email' && Request::get('direction') == 'desc' ? 'active' : '' }}"
                                            >{{ __('Email descending') }}</a>
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
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Date created') }}</th>
                                        <th width="10"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td class="align-middle">{{ $user->email }}</td>
                                            <td class="align-middle">{{ $user->name }}</td>
                                            <td class="align-middle">{{ $user->created_at }}</td>
                                            <td class="align-middle">
                                                <div class="btn-group btn-group-sm">
                                                    @can(\App\Enums\PermissionEnum::view_users()->value)
                                                        <a
                                                            href="{{ url('/users/' . $user->id) }}"
                                                            class="btn btn-default"
                                                        >{{ __('Detail') }}</a>
                                                    @endcan
                                                    @can(\App\Enums\PermissionEnum::delete_user()->value)
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger"
                                                            data-toggle="modal"
                                                            data-target="#modal-delete"
                                                            data-action="{{ url('/users/' . $user->id) }}"
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
                            {!! $users->withQueryString()->links() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
