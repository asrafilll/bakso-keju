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
                        href="{{ url('/orders') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Create order') }}</h1>
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
                        action="{{ url('/orders') }}"
                        method="POST"
                        novalidate
                    >
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group">
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
                                <div class="form-group">
                                    <label for="order_source_id">
                                        <span>{{ __('Order source') }}</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        id="order_source_id"
                                        name="order_source_id"
                                        class="form-control @error('order_source_id') is-invalid @enderror"
                                        style="width: 100%;"
                                    ></select>
                                    @error('order_source_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="reseller_id">
                                        <span>{{ __('Reseller') }}</span>
                                    </label>
                                    <select
                                        id="reseller_id"
                                        name="reseller_id"
                                        class="form-control @error('reseller_id') is-invalid @enderror"
                                        style="width: 100%;"
                                    >
                                        <option></option>
                                    </select>
                                    @error('reseller_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="customer_name">
                                        <span>{{ __('Customer name') }}</span>
                                        <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        id="customer_name"
                                        name="customer_name"
                                        class="form-control @error('customer_name') is-invalid @enderror"
                                    />
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">{{ __('Products') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
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
                                <div class="table-responsive">
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product') }}</th>
                                                <th
                                                    width="100px"
                                                    class="text-right"
                                                >{{ __('Quantity') }}</th>
                                                <th width="250px"class="text-right">{{ __('Total') }}</th>
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
                                                        name="line_items[<%= index %>][product_id]"
                                                        value="<%= lineItem.product_id %>"
                                                    >
                                                    <div><%= lineItem.product_name %></div>
                                                    <div><%= lineItem.product_price.toLocaleString('id') %></div>
                                                </td>
                                                <td class="text-right">
                                                    <input
                                                        type="number"
                                                        name="line_items[<%= index %>][quantity]"
                                                        class="form-control text-right line-item-quantity"
                                                        value="<%= lineItem.quantity %>"
                                                        min="1"
                                                        style="width: 100px;"
                                                        data-product-id="<%= lineItem.product_id %>"
                                                    >
                                                </td>
                                                <td class="text-right"><%= lineItem.total.toLocaleString('id') %></td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-default btn-sm line-item-delete"
                                                        data-product-id="<%= lineItem.product_id %>"
                                                    >
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <% }); %>
                                    </script>
                                    <script type="text/html" id="line-items-total-template">
                                        <tr>
                                            <th>{{ __('Sub Total') }}</th>
                                            <th class="text-right"><%= totalLineItemsQuantity.toLocaleString('id') %></th>
                                            <th class="text-right"><%= totalLineItemsPrice.toLocaleString('id') %></th>
                                            <th></th>
                                        </tr>
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">{{ __('Summary') }}</h5>
                            </div>
                            <div class="card-body">
                                <div
                                    class="table-responsive"
                                    id="order-summary"
                                >
                                    <script type="text/html" id="order-summary-template">
                                        <table class="table">
                                            <tr>
                                                <td>{{ __('Percentage Discount') }}</td>
                                                <td class="text-right"><%= percentageDiscount %></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Total Discount') }}</td>
                                                <td class="text-right"><%= totalDiscount.toLocaleString('id') %></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Sub Total') }}</td>
                                                <td class="text-right"><%= totalLineItemsPrice.toLocaleString('id') %></td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Total') }}</td>
                                                <td class="text-right"><%= totalPrice.toLocaleString('id') %></td>
                                            </tr>
                                        </table>
                                    </script>
                                </div>
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
    <script>
        $(function() {
            var $productId = $('#product_id');
            var $lineItems = $('#line-items');
            var $lineItemsTotal = $('#line-items-total');
            var $orderSummary = $('#order-summary')

            var lineItemsTemplate = $('#line-items-template').html();
            var lineItemsTotalTemplate = $('#line-items-total-template').html();
            var orderSummaryTemplate = $('#order-summary-template').html();

            var lineItems = new Map();
            var percentageDiscount = 0;
            var totalDiscount = 0;
            var totalLineItemsQuantity = 0;
            var totalLineItemsPrice = 0;
            var totalPrice = 0;

            $('#branch_id').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: '/orders/create?action=fetch-branches',
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

            $('#order_source_id').select2({
                theme: 'bootstrap4',
                ajax: {
                    url: '/orders/create?action=fetch-order-sources',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(orderSources) {
                        return {
                            results: orderSources.map(function(orderSource) {
                                return {
                                    id: orderSource.id,
                                    text: orderSource.name,
                                };
                            }),
                        };
                    },
                },
            });

            $('#reseller_id').select2({
                theme: 'bootstrap4',
                placeholder: '{{ __('Leave blank when order not from reseller') }}',
                allowClear: true,
                ajax: {
                    url: '/orders/create?action=fetch-resellers',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(resellers) {
                        return {
                            results: resellers.map(function(reseller) {
                                return {
                                    id: reseller.id,
                                    text: reseller.name,
                                    reseller: reseller,
                                };
                            }),
                        };
                    },
                },
            }).on('select2:select', function(e) {
                var reseller = e.params.data.reseller;
                percentageDiscount = reseller.percentage_discount;

                calculateTotal();
                renderOrderSummary();

                $('#customer_name').val(reseller.name);
            }).on('select2:unselect', function(e) {
                percentageDiscount = 0;

                calculateTotal();
                renderOrderSummary();

                $('#customer_name').val(null);
            });

            $productId.select2({
                theme: 'bootstrap4',
                placeholder: '{{ __('Search products') }}',
                ajax: {
                    url: '/orders/create?action=fetch-products',
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
            }).on('select2:select', function(e) {
                var product = e.params.data.product;

                createLineItem(product.id, product.name, product.price);
                $productId.val(null).trigger('change');
            });

            $('body').on('change', '.line-item-quantity', function() {
                var $this = $(this);
                var quantity = $this.val();
                var productId = $this.data('product-id');

                updateLineItemQuantityByProductId(productId, +quantity);
            });

            $('body').on('click', '.line-item-delete', function() {
                var $this = $(this);
                var productId = $(this).data('product-id');

                deleteLineItemByProductId(productId);
            });

            function createLineItem(product_id, product_name, product_price) {
                var existingLineItem = lineItems.get(product_id);
                var quantity = existingLineItem ? existingLineItem.quantity + 1 : 1;

                lineItems.set(product_id, {
                    product_id: product_id,
                    product_name: product_name,
                    product_price: product_price,
                    quantity: quantity,
                    total: product_price * quantity,
                });

                calculateTotal();
                renderOrderSummary();
                renderLineItems();
                renderLineItemsTotal();
            }

            function updateLineItemQuantityByProductId(product_id, quantity) {
                if (quantity < 1) {
                    lineItems.delete(product_id);
                } else {
                    var lineItem = lineItems.get(product_id);
                    lineItem.quantity = quantity;
                    lineItem.total = lineItem.product_price * lineItem.quantity;
                    lineItems.set(product_id, lineItem);
                }

                calculateTotal();
                renderOrderSummary();
                renderLineItems();
                renderLineItemsTotal();
            }

            function deleteLineItemByProductId(product_id) {
                lineItems.delete(product_id);

                calculateTotal();
                renderOrderSummary();
                renderLineItems();
                renderLineItemsTotal();
            }

            function calculateTotal() {
                totalLineItemsQuantity = 0;
                totalLineItemsPrice = 0;

                lineItems.forEach(function(lineItem) {
                    totalLineItemsQuantity += lineItem.quantity;
                    totalLineItemsPrice += lineItem.total;
                });

                totalDiscount = Math.round(totalLineItemsPrice * (percentageDiscount / 100));
                totalPrice = totalLineItemsPrice - totalDiscount;
            }

            function renderOrderSummary() {
                $orderSummary.html(
                    ejs.render(orderSummaryTemplate, {
                        percentageDiscount: percentageDiscount,
                        totalDiscount: totalDiscount,
                        totalLineItemsPrice: totalLineItemsPrice,
                        totalPrice: totalPrice,
                    })
                )
            }

            function renderLineItems() {
                $lineItems.html(
                    ejs.render(lineItemsTemplate, {
                        lineItems: lineItems,
                    })
                );
            }

            function renderLineItemsTotal() {
                $lineItemsTotal.html(
                    ejs.render(lineItemsTotalTemplate, {
                        totalLineItemsQuantity: totalLineItemsQuantity,
                        totalLineItemsPrice: totalLineItemsPrice,
                    })
                );
            }

            renderOrderSummary();
            renderLineItems();
            renderLineItemsTotal();
        });
    </script>
</x-app>
