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
                        href="{{ url('/product-categories') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ $productCategory->name }}</h1>
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
                    @can(\App\Enums\PermissionEnum::update_product_category()->value)
                        <form
                            action="{{ url('/product-categories/' . $productCategory->id) }}"
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
                                        id="name"
                                        type="text"
                                        name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ Request::old('name') ?? $productCategory->name }}"
                                    />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if ($parentProductCategories->count())
                                    <div class="form-group">
                                        <label for="parent_id">
                                            <span>{{ __('Parent') }}</span>
                                        </label>
                                        <select
                                            id="parent_id"
                                            name="parent_id"
                                            class="form-control @error('parent_id') is-invalid @enderror"
                                        >
                                            <option value=""></option>
                                            @foreach ($parentProductCategories as $parentProductCategory)
                                                <option
                                                    value="{{ $parentProductCategory->id }}"
                                                    @if (Request::old('parent_id') == $parentProductCategory->id ||
                                                        $productCategory->parent_id == $parentProductCategory->id) selected @endif
                                                >{{ $parentProductCategory->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                        @can(\App\Enums\PermissionEnum::update_product_category()->value)
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
