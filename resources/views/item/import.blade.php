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
                        href="{{ url('/items') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Import item') }}</h1>
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
                        action="{{ url('/items/import') }}"
                        method="POST"
                        novalidate
                        enctype="multipart/form-data"
                    >
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <p>Download a <a href="/templates/item_template.csv">sample CSV template</a> to see an example of the format required.</p>
                                <div class="form-group">
                                    <label for="item_category_id">
                                        <span>{{ __('Category') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        name="item_category_id"
                                        id="item_category_id"
                                        class="form-control @error('item_category_id') is-invalid @enderror"
                                    >
                                        <option value=""></option>
                                        @foreach ($itemCategories as $itemCategory)
                                            <option
                                                value="{{ $itemCategory->id }}"
                                                @if (Request::old('item_category_id') == $itemCategory->id) selected @endif
                                            >{{ $itemCategory->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('item_category_id')
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
