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
                    <h1 class="m-0">{{ __('Change Password') }}</h1>
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
                        action="{{ url('/profile/password') }}"
                        method="POST"
                        novalidate
                    >
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-body">
                            <div class="form-group">
                                    <label for="current_password">
                                        <span>{{ __('Current Password') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                    />
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">
                                        <span>{{ __('Password') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                    />
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">
                                        <span>{{ __('Password Confirmation') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="form-control @error('password') is-invalid @enderror"
                                    />
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
