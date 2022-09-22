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
    @if (Session::has('failed'))
        <script>
            toastr.error('{{ Session::has('failed') }}');
        </script>
    @endif

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-auto">
                    <a
                        href="{{ url('/products') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Import product') }}</h1>
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
                        action="{{ url('/products/import') }}"
                        method="POST"
                        novalidate
                        enctype="multipart/form-data"
                    >
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <p>Download a <a href="/templates/product_template.csv">sample CSV template</a> to see an example of the format required.</p>
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
                                        <option value="" hidden></option>
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
                                <div class="form-group">
                                    <label for="file">
                                        <span>{{ __('File') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="file"
                                        name="file"
                                        class="form-control-file @error('file') is-invalid @enderror"
                                        accept=".csv"
                                    />
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >{{ __('Import') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
