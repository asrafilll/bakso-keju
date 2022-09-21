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
                        href="{{ url('/items') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ $item->name }}</h1>
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
                        action="{{ url('/items/' . $item->id) }}"
                        method="POST"
                        novalidate
                    >
                        @csrf
                        @method('PUT')
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
                                        value="{{ Request::old('name') ?? $item->name }}"
                                    />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                        <option
                                            value=""
                                            hidden
                                        ></option>
                                        @foreach ($itemCategories as $itemCategory)
                                            <option
                                                value="{{ $itemCategory->id }}"
                                                @if ((Request::old('item_category_id') ?? $item->item_category_id) == $itemCategory->id) selected @endif
                                            >{{ $itemCategory->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('item_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="price">
                                        <span>{{ __('Price') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="price"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ Request::old('price') ?? $item->price }}"
                                    />
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
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
                                @foreach ($item->itemInventories as $itemInventory)
                                    <div class="row py-3 border-top align-items-center">
                                        <div class="col">{{ $itemInventory->branch->name }}</div>
                                        <div class="col">{{ $itemInventory->quantity }}</div>
                                    </div>
                                @endforeach
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
