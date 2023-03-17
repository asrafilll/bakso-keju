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
            toastr.error('{{ Session::get('failed') }}');
        </script>
    @endif

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row mb-2">
                <div class="col-auto">
                    <a href="{{ url('/item-inventories') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Create item inventory') }}</h1>
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
                    <form action="{{ url('/item-inventories') }}" method="POST" novalidate>
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="branch_id">
                                        <span>{{ __('Branch') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select id="branch_id" name="branch_id"
                                        class="form-control @error('branch_id') is-invalid @enderror"
                                        style="width: 100%;"></select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <script>
                                    $(function() {
                                        $('#branch_id').select2({
                                            theme: 'bootstrap4',
                                            ajax: {
                                                url: '/inventories/create?action=fetch-branches',
                                                dataType: 'json',
                                                delay: 250,
                                                processResults: function(branches) {
                                                    return {
                                                        results: branches.map(function(branch) {
                                                            return {
                                                                id: branch.id,
                                                                text: branch.name,
                                                            };
                                                        }),
                                                    };
                                                },
                                            },
                                        });
                                    });
                                </script>
                                <div class="form-group">
                                    <label for="item_id">
                                        <span>{{ __('Item') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select id="item_id" name="item_id"
                                        class="form-control @error('item_id') is-invalid @enderror"></select>
                                    @error('item_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <script>
                                    $(function() {
                                        $('#item_id').select2({
                                            theme: 'bootstrap4',
                                            ajax: {
                                                url: '/item-inventories/create?action=fetch-items',
                                                dataType: 'json',
                                                delay: 250,
                                                processResults: function(items) {
                                                    return {
                                                        results: items.map(function(item) {
                                                            return {
                                                                id: item.id,
                                                                text: item.name,
                                                            };
                                                        }),
                                                    };
                                                },
                                            },
                                        });
                                    });
                                </script>
                                <div class="form-group">
                                    <label for="quantity">
                                        <span>{{ __('Quantity') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="quantity"
                                        class="form-control @error('quantity') is-invalid @enderror" />
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
