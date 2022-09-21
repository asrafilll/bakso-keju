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

                                        $branchId.on('select2:select', function (e) {
                                            var branch = e.params.data.branch;

                                            ProductModule.init(branch.id);
                                            LineItemsModule.deleteAllLineItems();
                                        });

                                        function init() {
                                            $branchId.select2({
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
                                <div
                                    class="form-group"
                                    id="order-source-module"
                                >
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
                                <script>
                                    var OrderSourceModule = (function() {
                                        var $el = $('#order-source-module');
                                        var $orderSourceId = $el.find('#order_source_id');

                                        function init() {
                                            $orderSourceId.select2({
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
                                        }

                                        init();
                                    })();
                                </script>
                                <div
                                    class="form-group"
                                    id="reseller-module"
                                >
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
                                <script>
                                    var ResellerModule = (function() {
                                        var $el = $('#reseller-module');
                                        var $resellerId = $el.find('#reseller_id');

                                        $resellerId.on('select2:select', function(e) {
                                            var reseller = e.params.data.reseller;

                                            CustomerNameModule.setCustomerName(reseller.name);
                                            OrderSummary.setPercentageDiscount(reseller.percentage_discount);
                                        });

                                        $resellerId.on('select2:unselect', function(e) {
                                            CustomerNameModule.setCustomerName(null);
                                            OrderSummary.setPercentageDiscount(0);
                                        });

                                        function init() {
                                            $resellerId.select2({
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
                                            });
                                        }

                                        init();
                                    })();
                                </script>
                                <div
                                    class="form-group"
                                    id="customer-name-module"
                                >
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
                                <script>
                                    var CustomerNameModule = (function() {
                                        var $el = $('#customer-name-module');
                                        var $customerName = $el.find('#customer_name');

                                        function setCustomerName(value) {
                                            $customerName.val(value);
                                        }

                                        return {
                                            setCustomerName: setCustomerName,
                                        };
                                    })();
                                </script>
                            </div>
                        </div>
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
                                            LineItemsModule.addLineItem(product.id, product.name, +product.price);
                                        });

                                        function init(branchId = null) {
                                            var config = {
                                                theme: 'bootstrap4',
                                                placeholder: '{{ __('Search products') }}',
                                                ajax: null,
                                            };

                                            if (branchId) {
                                                config.ajax = {
                                                    url: '/orders/create?action=fetch-products&branch_id=' + branchId,
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
                                                };
                                            }

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
                                    id="line-items-module"
                                >
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
                                <script>
                                    var LineItemsModule = (function() {
                                        var $el = $('#line-items-module');
                                        var $lineItems = $el.find('#line-items');
                                        var $lineItemsTotal = $el.find('#line-items-total');

                                        var lineItemsTemplate = $el.find('#line-items-template').html();
                                        var lineItemsTotalTemplate = $el.find('#line-items-total-template').html();

                                        var lineItems = new Map();
                                        var totalLineItemsQuantity = 0;
                                        var totalLineItemsPrice = 0;

                                        $('body').on('blur', '.line-item-quantity', function() {
                                            var $this = $(this);
                                            var quantity = $this.val();
                                            var productId = $this.data('product-id');

                                            updateLineItem(productId, +quantity);
                                        });

                                        $('body').on('click', '.line-item-delete', function() {
                                            var $this = $(this);
                                            var productId = $(this).data('product-id');

                                            deleteLineItem(productId);
                                        });

                                        function addLineItem(product_id, product_name, product_price) {
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
                                            render()
                                        }

                                        function updateLineItem(product_id, quantity) {
                                            if (quantity < 1) {
                                                lineItems.delete(product_id);
                                            } else {
                                                var lineItem = lineItems.get(product_id);
                                                lineItem.quantity = quantity;
                                                lineItem.total = lineItem.product_price * lineItem.quantity;
                                                lineItems.set(product_id, lineItem);
                                            }

                                            calculateTotal();
                                            render();
                                        }

                                        function deleteLineItem(product_id) {
                                            lineItems.delete(product_id);

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
                                            totalLineItemsPrice = 0;

                                            lineItems.forEach(function(lineItem) {
                                                totalLineItemsQuantity += lineItem.quantity;
                                                totalLineItemsPrice += lineItem.total;
                                            });

                                            OrderSummary.setTotalLineItemsPrice(totalLineItemsPrice);
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
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">{{ __('Summary') }}</h5>
                            </div>
                            <div class="card-body">
                                <div
                                    class="table-responsive"
                                    id="order-summary-module"
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
                                <script>
                                    var OrderSummary = (function() {
                                        var $el = $('#order-summary-module');

                                        var template = $('#order-summary-template').html();

                                        var percentageDiscount = 0;
                                        var totalLineItemsPrice = 0;

                                        function setPercentageDiscount(value) {
                                            percentageDiscount = value;

                                            render();
                                        }

                                        function setTotalLineItemsPrice(value) {
                                            totalLineItemsPrice = value;

                                            render();
                                        }

                                        function getTotalDiscount() {
                                            return Math.round(totalLineItemsPrice * (percentageDiscount / 100));
                                        }

                                        function getTotalPrice() {
                                            return totalLineItemsPrice - getTotalDiscount();
                                        }

                                        function render() {
                                            $el.html(
                                                ejs.render(template, {
                                                    percentageDiscount: percentageDiscount,
                                                    totalLineItemsPrice: totalLineItemsPrice,
                                                    totalDiscount: getTotalDiscount(),
                                                    totalPrice: getTotalPrice(),
                                                })
                                            )
                                        }

                                        render();

                                        return {
                                            setPercentageDiscount: setPercentageDiscount,
                                            setTotalLineItemsPrice: setTotalLineItemsPrice,
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
