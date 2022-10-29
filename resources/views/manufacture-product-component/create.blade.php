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
                        href="{{ url('/manufacture-product-components') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Create manufacture product component') }}</h1>
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
                        action="{{ url('/manufacture-product-components') }}"
                        method="POST"
                        novalidate
                    >
                        @csrf
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

                                        function init() {
                                            $branchId.select2({
                                                theme: 'bootstrap4',
                                                ajax: {
                                                    url: '/manufacture-product-components/create?action=fetch-branches',
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
                                            LineItemsModule.addLineItem(productComponent.id, productComponent.name);
                                        });

                                        function init() {
                                            var config = {
                                                theme: 'bootstrap4',
                                                placeholder: '{{ __('Search product components') }}',
                                                ajax: {
                                                    url: '/manufacture-product-components/create?action=fetch-product-components',
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
                                                },
                                            };

                                            $productComponentId.select2(config);
                                        }

                                        init();
                                    })();
                                </script>
                                <div
                                    class="table-responsive"
                                    id="line-items-module"
                                >
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product') }}</th>
                                                <th
                                                    width="200px"
                                                    class="text-right"
                                                >{{ __('Price') }}</th>
                                                <th
                                                    width="100px"
                                                    class="text-right"
                                                >{{ __('Quantity') }}</th>
                                                <th
                                                    width="150px"
                                                    class="text-right"
                                                >{{ __('Total weight') }} (gram)</th>
                                                <th width="250px"class="text-right">{{ __('Total price') }}</th>
                                                <th width="10px"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="line-items"></tbody>
                                        <tfoot id="line-items-total"></tfoot>
                                    </table>
                                    <script type="text/html" id="line-items-template">
                                        <% lineItems.forEach(function (lineItem, index) { %>
                                            <tr>
                                                <td>
                                                    <input
                                                        type="hidden"
                                                        name="line_items[<%= index %>][product_component_id]"
                                                        value="<%= lineItem.product_component_id %>"
                                                    >
                                                    <div><%= lineItem.product_component_name %></div>
                                                </td>
                                                <td class="text-right">
                                                    <input
                                                        type="number"
                                                        name="line_items[<%= index %>][price]"
                                                        class="form-control text-right line-item-price"
                                                        value="<%= lineItem.price %>"
                                                        min="1"
                                                        data-product-component-id="<%= lineItem.product_component_id %>"
                                                    >
                                                </td>
                                                <td class="text-right">
                                                    <input
                                                        type="number"
                                                        name="line_items[<%= index %>][quantity]"
                                                        class="form-control text-right line-item-quantity"
                                                        value="<%= lineItem.quantity %>"
                                                        min="1"
                                                        data-product-component-id="<%= lineItem.product_component_id %>"
                                                    >
                                                </td>
                                                <td class="text-right">
                                                    <input
                                                        type="number"
                                                        name="line_items[<%= index %>][total_weight]"
                                                        class="form-control text-right line-item-total-weight"
                                                        value="<%= lineItem.total_weight %>"
                                                        min="1"
                                                        data-product-component-id="<%= lineItem.product_component_id %>"
                                                    >
                                                </td>
                                                <td class="text-right"><%= lineItem.total_price.toLocaleString('id') %></td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-default btn-sm line-item-delete"
                                                        data-product-component-id="<%= lineItem.product_component_id %>"
                                                    >
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <% }); %>
                                    </script>
                                    <script type="text/html" id="line-items-total-template">
                                        <tr>
                                            <th colspan="2">{{ __('Total') }}</th>
                                            <th class="text-right"><%= totalLineItemsQuantity.toLocaleString('id') %></th>
                                            <th class="text-right"><%= totalLineItemsWeight.toLocaleString('id') %></th>
                                            <th class="text-right"><%= totalLineItemsPrice.toLocaleString('id') %></th>
                                            <th></th>
                                        </tr>
                                    </script>
                                </div>
                                <script>
                                    var LineItemsModule = (function() {
                                        var $el = $('#line-items-module');
                                        var $lineItems = $el.find('#line-items');
                                        var $lineItemsTotal = $el.find('#line-items-total');

                                        var lineItemsTemplate = $el.find('#line-items-template').html();
                                        var lineItemsTotalTemplate = $el.find('#line-items-total-template').html();

                                        var lineItems = new Map();
                                        var totalLineItemsWeight = 0;
                                        var totalLineItemsQuantity = 0;
                                        var totalLineItemsPrice = 0;

                                        $('body').on('blur', '.line-item-price', function() {
                                            var $this = $(this);
                                            var price = $this.val();
                                            var productId = $this.data('product-component-id');

                                            updateLineItemPrice(productId, +price);
                                        });

                                        $('body').on('blur', '.line-item-quantity', function() {
                                            var $this = $(this);
                                            var quantity = $this.val();
                                            var productId = $this.data('product-component-id');

                                            updateLineItemQuantity(productId, +quantity);
                                        });

                                        $('body').on('blur', '.line-item-total-weight', function() {
                                            var $this = $(this);
                                            var total_weight = $this.val();
                                            var productId = $this.data('product-component-id');

                                            updateLineItemTotalWeight(productId, +total_weight);
                                        });

                                        $('body').on('click', '.line-item-delete', function() {
                                            var $this = $(this);
                                            var productId = $(this).data('product-component-id');

                                            deleteLineItem(productId);
                                        });

                                        function addLineItem(product_component_id, product_component_name) {
                                            var existingLineItem = lineItems.get(product_component_id);
                                            var price = existingLineItem ? existingLineItem.price : 0;
                                            var quantity = (existingLineItem ? existingLineItem.quantity : 0) + 1;
                                            var total_weight = existingLineItem ? existingLineItem.total_weight : 0;

                                            lineItems.set(product_component_id, {
                                                product_component_id: product_component_id,
                                                product_component_name: product_component_name,
                                                price: price,
                                                quantity: quantity,
                                                total_weight: total_weight,
                                                total_price: price * quantity,
                                            });

                                            calculateTotal();
                                            render()
                                        }

                                        function updateLineItemPrice(product_component_id, price) {
                                            var lineItem = lineItems.get(product_component_id);
                                            lineItem.price = price;
                                            lineItem.total_price = lineItem.price * lineItem.quantity;
                                            lineItems.set(product_component_id, lineItem);

                                            calculateTotal();
                                            render();
                                        }

                                        function updateLineItemQuantity(product_component_id, quantity) {
                                            if (quantity < 1) {
                                                lineItems.delete(product_component_id);
                                            } else {
                                                var lineItem = lineItems.get(product_component_id);
                                                lineItem.quantity = quantity;
                                                lineItem.total_price = lineItem.price * lineItem.quantity;
                                                lineItems.set(product_component_id, lineItem);
                                            }

                                            calculateTotal();
                                            render();
                                        }

                                        function updateLineItemTotalWeight(product_component_id, total_weight) {
                                            var lineItem = lineItems.get(product_component_id);
                                            lineItem.total_weight = total_weight;
                                            lineItems.set(product_component_id, lineItem);

                                            calculateTotal();
                                            render();
                                        }

                                        function deleteLineItem(product_component_id) {
                                            lineItems.delete(product_component_id);

                                            calculateTotal();
                                            render();
                                        }

                                        function deleteAllLineItems() {
                                            lineItems.clear();

                                            calculateTotal();
                                            render();
                                        }

                                        function calculateTotal() {
                                            totalLineItemsQuantity = 0;
                                            totalLineItemsWeight = 0;
                                            totalLineItemsPrice = 0;

                                            lineItems.forEach(function(lineItem) {
                                                totalLineItemsQuantity += lineItem.quantity;
                                                totalLineItemsWeight += lineItem.total_weight;
                                                totalLineItemsPrice += lineItem.total_price;
                                            });
                                        }

                                        function render() {
                                            $lineItems.html(
                                                ejs.render(lineItemsTemplate, {
                                                    lineItems: lineItems,
                                                })
                                            );
                                            $lineItemsTotal.html(
                                                ejs.render(lineItemsTotalTemplate, {
                                                    totalLineItemsQuantity: totalLineItemsQuantity,
                                                    totalLineItemsWeight: totalLineItemsWeight,
                                                    totalLineItemsPrice: totalLineItemsPrice,
                                                })
                                            );
                                        }

                                        render();

                                        return {
                                            addLineItem: addLineItem,
                                            deleteAllLineItems: deleteAllLineItems,
                                        };
                                    })();
                                </script>
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
