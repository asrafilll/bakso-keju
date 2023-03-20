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
                    <h1 class="m-0">{{ __('New hampers') }}</h1>
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
                    <form action="{{ url('/product-hampers') }}" method="POST" novalidate>
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">
                                        <span>{{ __('Name') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ Request::old('name') }}" />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="price">
                                        <span>{{ __('Default Price') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="price"
                                        class="form-control @error('price') is-invalid @enderror"
                                        value="{{ Request::old('price') }}" />
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="form-group" id="product-module">
                                    <select id="product_id"
                                        class="form-control @error('product_id') is-invalid @enderror"
                                        style="width: 100%;">
                                        <option></option>
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <script>
                                    var ProductModule = (function() {
                                        var $el = $('#product-module');
                                        var $productId = $el.find('#product_id');

                                        $productId.on('select2:select', function(e) {
                                            var product = e.params.data.product;

                                            $productId.val(null).trigger('change');
                                            LineProductsModule.addLineProduct(
                                                product.id,
                                                product.name,
                                                product.price
                                            );
                                        });

                                        function init(branchId = null) {
                                            var config = {
                                                theme: 'bootstrap4',
                                                placeholder: '{{ __('Search products') }}',
                                                ajax: {
                                                    url: '/product-hampers/create?action=fetch-products',
                                                    dataType: 'json',
                                                    delay: 250,
                                                    processResults: function(products) {
                                                        return {
                                                            results: products.map(function(product) {
                                                                return {
                                                                    id: product.id,
                                                                    text: product.name,
                                                                    product: product,
                                                                };
                                                            }),
                                                        };
                                                    },
                                                },
                                            };

                                            $productId.select2(config);
                                        }

                                        init();

                                        return {
                                            init: init,
                                        };
                                    })();
                                </script>
                                <div class="table-responsive" id="line-products-module">
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product') }}</th>
                                                <th>{{ __('Price') }}</th>
                                                <th width="100px" class="text-right">{{ __('Quantity') }}</th>
                                                <th width="10px"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="line-products"></tbody>
                                        <tfoot id="line-products-total"></tfoot>
                                    </table>
                                    <script type="text/html" id="line-products-template">
                                        <% lineProducts.forEach(function (lineProduct, index) { %>
                                            <tr>
                                                <td>
                                                    <input
                                                        type="hidden"
                                                        name="products[<%= index %>][product_id]"
                                                        value="<%= lineProduct.product_id %>"
                                                    >
                                                    <div><%= lineProduct.product_name %></div>
                                                </td>
                                                <td>
                                                    <input
                                                        type="hidden"
                                                        name="products[<%= index %>][product_id]"
                                                        value="<%= lineProduct.product_id %>"
                                                    >
                                                    <div><%= lineProduct.product_price %></div>
                                                </td>
                                                <td class="text-right">
                                                    <input
                                                        type="number"
                                                        name="products[<%= index %>][quantity]"
                                                        class="form-control text-right line-product-quantity"
                                                        value="<%= lineProduct.quantity %>"
                                                        min="1"
                                                        data-product-id="<%= lineProduct.product_id %>"
                                                    >
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-default btn-sm line-product-delete"
                                                        data-product-id="<%= lineProduct.product_id %>"
                                                    >
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <% }); %>
                                    </script>
                                    <script type="text/html" id="line-products-total-template">
                                        <tr>
                                            <th>{{ __('Total') }}</th>
                                            <th></th>
                                            <th class="text-right"><%= totalLineProductsQuantity.toLocaleString('id') %></th>
                                            <th></th>
                                        </tr>
                                    </script>
                                </div>
                                <script>
                                    var LineProductsModule = (function() {
                                        var $el = $('#line-products-module');
                                        var $lineProducts = $el.find('#line-products');
                                        var $lineProductsTotal = $el.find('#line-products-total');

                                        var lineProductsTemplate = $el.find('#line-products-template').html();
                                        var lineProductsTotalTemplate = $el.find('#line-products-total-template').html();

                                        var lineProducts = new Map();
                                        var totalLineProductsQuantity = 0;

                                        $('body').on('blur', '.line-product-quantity', function() {
                                            var $this = $(this);
                                            var quantity = $this.val();
                                            var productId = $this.data('product-id');

                                            updateLineProductQuantity(
                                                productId,
                                                +quantity
                                            );
                                        });

                                        $('body').on('click', '.line-product-delete', function() {
                                            var $this = $(this);
                                            var productId = $(this).data('product-id');

                                            deleteLineProduct(productId);
                                        });

                                        function addLineProduct(
                                            product_id,
                                            product_name,
                                            product_price
                                        ) {
                                            var existingLineItem = lineProducts.get(product_id);
                                            var quantity = (existingLineItem ? existingLineItem.quantity : 0) + 1;

                                            lineProducts.set(product_id, {
                                                product_id: product_id,
                                                product_name: product_name,
                                                product_price: product_price,
                                                quantity: quantity,
                                            });

                                            calculateTotal();
                                            render()
                                        }

                                        function updateLineProductQuantity(
                                            product_id,
                                            quantity
                                        ) {
                                            if (quantity < 1) {
                                                lineProducts.delete(product_id);
                                            } else {
                                                var lineProduct = lineProducts.get(product_id);
                                                lineProduct.quantity = quantity;
                                                lineProduct.total_price = lineProduct.price * lineProduct.quantity;
                                                lineProducts.set(product_id, lineProduct);
                                            }

                                            calculateTotal();
                                            render();
                                        }

                                        function deleteLineProduct(product_id) {
                                            lineProducts.delete(product_id);

                                            calculateTotal();
                                            render();
                                        }

                                        function deleteAllLineProductComponents() {
                                            lineProducts.clear();

                                            calculateTotal();
                                            render();
                                        }

                                        function calculateTotal() {
                                            totalLineProductsQuantity = 0;

                                            lineProducts.forEach(function(lineProduct) {
                                                totalLineProductsQuantity += lineProduct.quantity;
                                            });
                                        }

                                        function render() {
                                            $lineProducts.html(
                                                ejs.render(lineProductsTemplate, {
                                                    lineProducts: lineProducts,
                                                })
                                            );
                                            $lineProductsTotal.html(
                                                ejs.render(lineProductsTotalTemplate, {
                                                    totalLineProductsQuantity: totalLineProductsQuantity,
                                                })
                                            );
                                        }

                                        render();

                                        return {
                                            addLineProduct: addLineProduct,
                                            deleteAllLineProductComponents: deleteAllLineProductComponents,
                                        };
                                    })();
                                </script>
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
