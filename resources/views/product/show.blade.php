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
                        href="{{ url('/products') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ $product->name }}</h1>
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
                    @can(\App\Enums\PermissionEnum::update_product()->value)
                        <form
                            action="{{ url('/products/' . $product->id) }}"
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
                                        value="{{ Request::old('name') ?? $product->name }}"
                                    />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                        <option
                                            value=""
                                            hidden
                                        ></option>
                                        @foreach ($productCategories as $productCategory)
                                            <option
                                                value="{{ $productCategory->id }}"
                                                @if ((Request::old('product_category_id') ?? $product->product_category_id) == $productCategory->id) selected @endif
                                            >{{ $productCategory->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('product_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="price">
                                        <span>{{ __('Default Price') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="price"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ Request::old('price') ?? $product->price }}"
                                    />
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">{{ __('Price per Order Source') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row py-3 font-weight-bold">
                                    <div class="col">{{ __('Order Source') }}</div>
                                    <div class="col">{{ __('Price') }}</div>
                                </div>
                                @foreach ($orderSources as $key => $orderSource)
                                    <div class="row py-3 border-top align-items-center">
                                        <div class="col">
                                            <input
                                                type="hidden"
                                                class="form-control"
                                                name="prices[{{ $key }}][order_source_id]"
                                                value="{{ $orderSource->id }}"
                                            />
                                            <span>{{ $orderSource->name }}</span>
                                        </div>
                                        <div class="col">
                                            <input
                                                type="number"
                                                class="form-control"
                                                name="prices[{{ $key }}][price]"
                                                value="{{ $product->productPrices->firstWhere('order_source_id', $orderSource->id)->price }}"
                                            />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title font-weight-bold">{{ __('Inventory') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row py-3 font-weight-bold">
                                    <div class="col">{{ __('Branch') }}</div>
                                    <div class="col">{{ __('Quantity') }}</div>
                                </div>
                                @foreach ($product->productInventories as $productInventory)
                                    <div class="row py-3 border-top align-items-center">
                                        <div class="col">{{ $productInventory->branch->name }}</div>
                                        <div class="col">{{ $productInventory->quantity }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @can(\App\Enums\PermissionEnum::update_product()->value)
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
