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
                        href="{{ url('/purchases') }}"
                        class="btn btn-default"
                    >
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                <div class="col-auto">
                    <h1 class="m-0">{{ __('Create purchase') }}</h1>
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
                        action="{{ url('/purchases') }}"
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

                                        function init() {
                                            $branchId.select2({
                                                theme: 'bootstrap4',
                                                ajax: {
                                                    url: '/purchases/create?action=fetch-branches',
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
                                <h5 class="card-title">{{ __('Items') }}</h5>
                            </div>
                            <div class="card-body">
                                <div
                                    class="form-group"
                                    id="item-module"
                                >
                                    <select
                                        id="item_id"
                                        name="item_id"
                                        class="form-control @error('item_id') is-invalid @enderror"
                                        style="width: 100%;"
                                    >
                                        <option></option>
                                    </select>
                                    @error('item_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <script>
                                    var ItemModule = (function() {
                                        var $el = $('#item-module');
                                        var $itemId = $el.find('#item_id');

                                        $itemId.on('select2:select', function(e) {
                                            var item = e.params.data.item;

                                            $itemId.val(null).trigger('change');
                                            LineItemsModule.addLineItem(item.id, item.name, +item.price);
                                        });

                                        function init() {
                                            $itemId.select2({
                                                theme: 'bootstrap4',
                                                placeholder: '{{ __('Search items') }}',
                                                ajax: {
                                                    url: '/purchases/create?action=fetch-items',
                                                    dataType: 'json',
                                                    delay: 250,
                                                    processResults: function(items) {
                                                        return {
                                                            results: items.map(function(item) {
                                                                return {
                                                                    id: item.id,
                                                                    text: item.name,
                                                                    item: item,
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
                                    class="table-responsive"
                                    id="line-items-module"
                                >
                                    <table class="table text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Item') }}</th>
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
                                                        name="line_items[<%= index %>][item_id]"
                                                        value="<%= lineItem.item_id %>"
                                                    >
                                                    <div><%= lineItem.item_name %></div>
                                                    <div><%= lineItem.item_price.toLocaleString('id') %></div>
                                                </td>
                                                <td class="text-right">
                                                    <input
                                                        type="number"
                                                        name="line_items[<%= index %>][quantity]"
                                                        class="form-control text-right line-item-quantity"
                                                        value="<%= lineItem.quantity %>"
                                                        min="1"
                                                        style="width: 100px;"
                                                        data-item-id="<%= lineItem.item_id %>"
                                                    >
                                                </td>
                                                <td class="text-right"><%= lineItem.total.toLocaleString('id') %></td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        class="btn btn-default btn-sm line-item-delete"
                                                        data-item-id="<%= lineItem.item_id %>"
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
                                            var itemId = $this.data('item-id');

                                            updateLineItem(itemId, +quantity);
                                        });

                                        $('body').on('click', '.line-item-delete', function() {
                                            var $this = $(this);
                                            var itemId = $(this).data('item-id');

                                            deleteLineItem(itemId);
                                        });

                                        function addLineItem(item_id, item_name, item_price) {
                                            var existingLineItem = lineItems.get(item_id);
                                            var quantity = existingLineItem ? existingLineItem.quantity + 1 : 1;

                                            lineItems.set(item_id, {
                                                item_id: item_id,
                                                item_name: item_name,
                                                item_price: item_price,
                                                quantity: quantity,
                                                total: item_price * quantity,
                                            });

                                            calculateTotal();
                                            render()
                                        }

                                        function updateLineItem(item_id, quantity) {
                                            if (quantity < 1) {
                                                lineItems.delete(item_id);
                                            } else {
                                                var lineItem = lineItems.get(item_id);
                                                lineItem.quantity = quantity;
                                                lineItem.total = lineItem.item_price * lineItem.quantity;
                                                lineItems.set(item_id, lineItem);
                                            }

                                            calculateTotal();
                                            render();
                                        }

                                        function deleteLineItem(item_id) {
                                            lineItems.delete(item_id);

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
                                    id="purchase-summary-module"
                                >
                                    <script type="text/html" id="purchase-summary-template">
                                        <table class="table">
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
                                        var $el = $('#purchase-summary-module');

                                        var template = $('#purchase-summary-template').html();

                                        var totalLineItemsPrice = 0;

                                        function setTotalLineItemsPrice(value) {
                                            totalLineItemsPrice = value;

                                            render();
                                        }

                                        function getTotalPrice() {
                                            return totalLineItemsPrice;
                                        }

                                        function render() {
                                            $el.html(
                                                ejs.render(template, {
                                                    totalLineItemsPrice: totalLineItemsPrice,
                                                    totalPrice: getTotalPrice(),
                                                })
                                            )
                                        }

                                        render();

                                        return {
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
