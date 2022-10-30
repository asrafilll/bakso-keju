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
                    <a
                        href="{{ url('/manufacture-products') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Create manufacture product') }}</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <form
            action="{{ url('/manufacture-products') }}"
            method="POST"
            novalidate
        >
            @csrf
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div
                                    class="form-group"
                                    id="created_at-module"
                                >
                                    <label for="created_at">
                                        <span>{{ __('Date') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        id="created_at"
                                        type="text"
                                        name="created_at"
                                        class="form-control datetimepicker-input"
                                        data-target="#created_at"
                                        data-toggle="datetimepicker"
                                    />
                                </div>
                                <script>
                                    var CreatedAtModule = (function() {
                                        var $el = $('#created_at-module');
                                        var $createdAt = $el.find('#created_at')

                                        function init() {
                                            $createdAt.datetimepicker({
                                                format: 'YYYY-MM-DD HH:mm:ss',
                                                icons: {
                                                    time: 'far fa-clock'
                                                },
                                            });
                                        }

                                        init();
                                    })();
                                </script>
                                <div
                                    class="form-group"
                                    id="branch-module"
                                >
                                    <label for="branch_id">
                                        <span>{{ __('Branch') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        id="branch_id"
                                        name="branch_id"
                                        class="form-control @error('branch_id') is-invalid @enderror"
                                        style="width: 100%;"
                                    ></select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <script>
                                    var BranchModule = (function() {
                                        var $el = $('#branch-module');
                                        var $branchId = $el.find('#branch_id')

                                        $branchId.on('select2:select', function(e) {
                                            var branch = e.params.data.branch;

                                            ProductComponentModule.init(branch.id);
                                            LineProductComponentsModule.deleteAllLineProductComponents();
                                        });

                                        function init() {
                                            $branchId.select2({
                                                theme: 'bootstrap4',
                                                ajax: {
                                                    url: '/manufacture-products/create?action=fetch-branches',
                                                    dataType: 'json',
                                                    delay: 250,
                                                    processResults: function(branches) {
                                                        return {
                                                            results: branches.map(function(branch) {
                                                                return {
                                                                    id: branch.id,
                                                                    text: branch.name,
                                                                    branch: branch,
                                                                };
                                                            }),
                                                        };
                                                    },
                                                },
                                            });
                                        }

                                        init();
                                    })()
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">{{ __('Product components') }}</h5>
                            </div>
                            <div class="card-body">
                                <div
                                    class="form-group"
                                    id="product-component-module"
                                >
                                    <select
                                        id="product_component_id"
                                        name="product_component_id"
                                        class="form-control @error('product_component_id') is-invalid @enderror"
                                        style="width: 100%;"
                                    >
                                        <option></option>
                                    </select>
                                    @error('product_component_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <script>
                                    var ProductComponentModule = (function() {
                                        var $el = $('#product-component-module');
                                        var $productComponentId = $el.find('#product_component_id');

                                        $productComponentId.on('select2:select', function(e) {
                                            var productComponent = e.params.data.productComponent;

                                            $productComponentId.val(null).trigger('change');
                                            LineProductComponentsModule.addLineProductComponent(
                                                productComponent.id,
                                                productComponent.name
                                            );
                                        });

                                        function init(branchId = null) {
                                            var config = {
                                                theme: 'bootstrap4',
                                                placeholder: '{{ __('Search product components') }}',
                                                ajax: null,
                                            };

                                            if (branchId) {
                                                config.ajax = {
                                                    url: '/manufacture-products/create?action=fetch-product-components&branch_id=' + branchId,
                                                    dataType: 'json',
                                                    delay: 250,
                                                    processResults: function(productComponents) {
                                                        return {
                                                            results: productComponents.map(function(productComponent) {
                                                                return {
                                                                    id: productComponent.id,
                                                                    text: productComponent.name,
                                                                    productComponent: productComponent,
                                                                };
                                                            }),
                                                        };
                                                    },
                                                };
                                            }

                                            $productComponentId.select2(config);
                                        }

                                        init();

                                        return {
                                            init: init,
                                        };
                                    })();
                                </script>
                                <div
                                    class="table-responsive"
                                    id="line-product-components-module"
                                >
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product component') }}</th>
                                                <th
                                                    width="100px"
                                                    class="text-right"
                                                >{{ __('Quantity') }}</th>
                                                <th width="10px"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="line-product-components"></tbody>
                                        <tfoot id="line-product-components-total"></tfoot>
                                    </table>
                                    <script type="text/html" id="line-product-components-template">
                                        <% lineProductComponents.forEach(function (lineProductComponent, index) { %>
                                            <tr>
                                                <td>
                                                    <input
                                                        type="hidden"
                                                        name="line_product_components[<%= index %>][product_component_id]"
                                                        value="<%= lineProductComponent.product_component_id %>"
                                                    >
                                                    <div><%= lineProductComponent.product_component_name %></div>
                                                </td>
                                                <td class="text-right">
                                                    <input
                                                        type="number"
                                                        name="line_product_components[<%= index %>][quantity]"
                                                        class="form-control text-right line-product-component-quantity"
                                                        value="<%= lineProductComponent.quantity %>"
                                                        min="1"
                                                        data-product-component-id="<%= lineProductComponent.product_component_id %>"
                                                    >
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-default btn-sm line-product-component-delete"
                                                        data-product-component-id="<%= lineProductComponent.product_component_id %>"
                                                    >
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <% }); %>
                                    </script>
                                    <script type="text/html" id="line-product-components-total-template">
                                        <tr>
                                            <th>{{ __('Total') }}</th>
                                            <th class="text-right"><%= totalLineProductComponentsQuantity.toLocaleString('id') %></th>
                                            <th></th>
                                        </tr>
                                    </script>
                                </div>
                                <script>
                                    var LineProductComponentsModule = (function() {
                                        var $el = $('#line-product-components-module');
                                        var $lineProductComponents = $el.find('#line-product-components');
                                        var $lineProductComponentsTotal = $el.find('#line-product-components-total');

                                        var lineProductComponentsTemplate = $el.find('#line-product-components-template').html();
                                        var lineProductComponentsTotalTemplate = $el.find('#line-product-components-total-template').html();

                                        var lineProductComponents = new Map();
                                        var totalLineProductComponentsQuantity = 0;

                                        $('body').on('blur', '.line-product-component-quantity', function() {
                                            var $this = $(this);
                                            var quantity = $this.val();
                                            var productComponentId = $this.data('product-component-id');

                                            updateLineProductComponentQuantity(
                                                productComponentId,
                                                +quantity
                                            );
                                        });

                                        $('body').on('click', '.line-product-component-delete', function() {
                                            var $this = $(this);
                                            var productComponentId = $(this).data('product-component-id');

                                            deleteLineProductComponent(productComponentId);
                                        });

                                        function addLineProductComponent(
                                            product_component_id,
                                            product_component_name
                                        ) {
                                            var existingLineItem = lineProductComponents.get(product_component_id);
                                            var quantity = (existingLineItem ? existingLineItem.quantity : 0) + 1;

                                            lineProductComponents.set(product_component_id, {
                                                product_component_id: product_component_id,
                                                product_component_name: product_component_name,
                                                quantity: quantity,
                                            });

                                            calculateTotal();
                                            render()
                                        }

                                        function updateLineProductComponentQuantity(
                                            product_component_id,
                                            quantity
                                        ) {
                                            if (quantity < 1) {
                                                lineProductComponents.delete(product_component_id);
                                            } else {
                                                var lineProductComponent = lineProductComponents.get(product_component_id);
                                                lineProductComponent.quantity = quantity;
                                                lineProductComponent.total_price = lineProductComponent.price * lineProductComponent.quantity;
                                                lineProductComponents.set(product_component_id, lineProductComponent);
                                            }

                                            calculateTotal();
                                            render();
                                        }

                                        function deleteLineProductComponent(product_component_id) {
                                            lineProductComponents.delete(product_component_id);

                                            calculateTotal();
                                            render();
                                        }

                                        function deleteAllLineProductComponents() {
                                            lineProductComponents.clear();

                                            calculateTotal();
                                            render();
                                        }

                                        function calculateTotal() {
                                            totalLineProductComponentsQuantity = 0;

                                            lineProductComponents.forEach(function(lineProductComponent) {
                                                totalLineProductComponentsQuantity += lineProductComponent.quantity;
                                            });
                                        }

                                        function render() {
                                            $lineProductComponents.html(
                                                ejs.render(lineProductComponentsTemplate, {
                                                    lineProductComponents: lineProductComponents,
                                                })
                                            );
                                            $lineProductComponentsTotal.html(
                                                ejs.render(lineProductComponentsTotalTemplate, {
                                                    totalLineProductComponentsQuantity: totalLineProductComponentsQuantity,
                                                })
                                            );
                                        }

                                        render();

                                        return {
                                            addLineProductComponent: addLineProductComponent,
                                            deleteAllLineProductComponents: deleteAllLineProductComponents,
                                        };
                                    })();
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">{{ __('Products') }}</h5>
                            </div>
                            <div class="card-body">
                                <div
                                    class="form-group"
                                    id="product-module"
                                >
                                    <select
                                        id="product_id"
                                        name="product_id"
                                        class="form-control @error('product_id') is-invalid @enderror"
                                        style="width: 100%;"
                                    >
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
                                                product.name
                                            );
                                        });

                                        function init(branchId = null) {
                                            var config = {
                                                theme: 'bootstrap4',
                                                placeholder: '{{ __('Search products') }}',
                                                ajax: {
                                                    url: '/manufacture-products/create?action=fetch-products',
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
                                <div
                                    class="table-responsive"
                                    id="line-products-module"
                                >
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product') }}</th>
                                                <th
                                                    width="100px"
                                                    class="text-right"
                                                >{{ __('Quantity') }}</th>
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
                                                        name="line_products[<%= index %>][product_id]"
                                                        value="<%= lineProduct.product_id %>"
                                                    >
                                                    <div><%= lineProduct.product_name %></div>
                                                </td>
                                                <td class="text-right">
                                                    <input
                                                        type="number"
                                                        name="line_products[<%= index %>][quantity]"
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
                                            product_name
                                        ) {
                                            var existingLineItem = lineProducts.get(product_id);
                                            var quantity = (existingLineItem ? existingLineItem.quantity : 0) + 1;

                                            lineProducts.set(product_id, {
                                                product_id: product_id,
                                                product_name: product_name,
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
                    </div>
                    <div class="col-12">
                        <button
                            type="submit"
                            class="btn btn-primary"
                        >{{ __('Save') }}</button>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </form>
    </section>
    <!-- /.content -->
</x-app>
