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
                    <h1 class="m-0">{{ $role->name }}</h1>
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
                    @can(\App\Enums\PermissionEnum::update_role()->value)
                        <form
                            action="{{ url('/roles/' . $role->id) }}"
                            method="POST"
                            novalidate
                        >
                            @csrf
                            @method('PUT')
                        @endcan
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
                                        value="{{ Request::old('name') ?? $role->name }}"
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
                                    <div class="col">
                                        @foreach ($viewPermissions as $permission)
                                            <div class="icheck-primary">
                                                <input
                                                    type="checkbox"
                                                    id="permission_{{ $permission->id }}"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    @if ($role->permissions->contains('id', $permission->id)) checked @endif
                                                />
                                                <label for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col">
                                        @foreach ($createPermissions as $permission)
                                            <div class="icheck-primary">
                                                <input
                                                    type="checkbox"
                                                    id="permission_{{ $permission->id }}"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    @if ($role->permissions->contains('id', $permission->id)) checked @endif
                                                />
                                                <label for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col">
                                        @foreach ($updatePermissions as $permission)
                                            <div class="icheck-primary">
                                                <input
                                                    type="checkbox"
                                                    id="permission_{{ $permission->id }}"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    @if ($role->permissions->contains('id', $permission->id)) checked @endif
                                                />
                                                <label for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="col">
                                        @foreach ($deletePermissions as $permission)
                                            <div class="icheck-primary">
                                                <input
                                                    type="checkbox"
                                                    id="permission_{{ $permission->id }}"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    @if ($role->permissions->contains('id', $permission->id)) checked @endif
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
                        @can(\App\Enums\PermissionEnum::update_role()->value)
                            <button
                                type="submit"
                                class="btn btn-primary"
                            >{{ __('Save') }}</button>
                        </form>
                    @endcan
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
