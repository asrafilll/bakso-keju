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
                    <a href="{{ url('/product-hampers') }}" class="btn btn-default">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ $productHamper->name }}</h1>
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
                    <form action="{{ url('/product-hampers/' . $productHamper->id) }}" method="POST" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">
                                        <span>{{ __('Name') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ Request::old('name', $productHamper->name) }}" />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="charge">
                                        <span>{{ __('Charge price') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="charge"
                                        class="form-control @error('charge') is-invalid @enderror"
                                        value="{{ Request::old('charge', $productHamper->charge) }}" />
                                    @error('charge')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product') }}</th>
                                                <th width="100px" class="text-right">{{ __('Quantity') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($productHamper->productHamperLines as $product)
                                                <tr>
                                                    <td>{{ $product->product->name }}</td>
                                                    <td class="text-right">
                                                        {{ $product->quantity }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-delete"
                            data-action="{{ url('/product-hampers/' . $productHamper->id) }}">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</x-app>
