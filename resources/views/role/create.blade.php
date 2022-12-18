<x-app>
    @if ($errors->any())
        <script>
            toastr.error('{{ $errors->first() }}');
        </script>
    @endif
    @if (Session::has('success'))
        <script>
            toastr.success('{{ Session::get('success') }}');
        </script>
    @endif

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-auto">
                    <a
                        href="{{ url('/roles') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ __('New role') }}</h1>
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
                    <form
                        action="{{ url('/roles') }}"
                        method="POST"
                        novalidate
                    >
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">
                                        <span>{{ __('Name') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ Request::old('name') }}"
                                    />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Permissions') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h5>{{ __('View Permissions') }}</h5>
                                        @foreach ($viewPermissions as $permission)
                                            <div class="icheck-primary">
                                                <input
                                                    type="checkbox"
                                                    id="permission_{{ $permission->id }}"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                />
                                                <label for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h5>{{ __('Create Permissions') }}</h5>
                                        @foreach ($createPermissions as $permission)
                                            <div class="icheck-primary">
                                                <input
                                                    type="checkbox"
                                                    id="permission_{{ $permission->id }}"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                />
                                                <label for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h5>{{ __('Update Permissions') }}</h5>
                                        @foreach ($updatePermissions as $permission)
                                            <div class="icheck-primary">
                                                <input
                                                    type="checkbox"
                                                    id="permission_{{ $permission->id }}"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                />
                                                <label for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <h5>{{ __('Delete Permissions') }}</h5>
                                        @foreach ($deletePermissions as $permission)
                                            <div class="icheck-primary">
                                                <input
                                                    type="checkbox"
                                                    id="permission_{{ $permission->id }}"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                />
                                                <label for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
