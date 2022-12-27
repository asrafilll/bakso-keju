@props(['branches', 'products', 'summary'])

<table class="table table-bordered table-hover text-nowrap">
    <thead>
        <tr>
            <th
                rowspan="3"
                class="align-middle text-center"
            >{{ __('No') }}</th>
            <th
                rowspan="3"
                class="align-middle"
            >{{ __('Product') }}</th>
            <th
                rowspan="3"
                class="align-middle text-center"
            >{{ __('In Pcs') }}</th>
            <th
                rowspan="3"
                class="align-middle text-center"
            >{{ __('In Value') }}</th>
            @foreach ($branches as $branch)
                <th
                    colspan="{{ count($branch['order_sources']) * 2 }}"
                    class="text-center"
                >{{ $branch['name'] }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($branches as $branch)
                @foreach ($branch['order_sources'] as $orderSource)
                    <th
                        colspan="2"
                        class="text-center"
                    >{{ $orderSource['name'] }}</th>
                @endforeach
            @endforeach
        </tr>
        <tr>
            @foreach ($branches as $branch)
                @foreach ($branch['order_sources'] as $orderSource)
                    <th>{{ __('In Pcs') }}</th>
                    <th>{{ __('In Value') }}</th>
                @endforeach
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td class="text-right">{{ $loop->iteration }}</td>
                <td>{{ $product['product_name'] }}</td>
                <td class="text-right">
                    {{ Request::get('action') === 'export' ? $product['total_quantity'] : $product['idr_total_quantity'] }}
                </td>
                <td class="text-right">
                    {{ Request::get('action') === 'export' ? $product['total_price'] : $product['idr_total_price'] }}
                </td>
                @foreach ($product['branches'] as $branch)
                    @foreach ($branch['order_sources'] as $orderSource)
                        <td class="text-right">
                            {{ Request::get('action') === 'export' ? $orderSource['total_quantity'] : $orderSource['idr_total_quantity'] }}
                        </td>
                        <td class="text-right">
                            {{ Request::get('action') === 'export' ? $orderSource['total_price'] : $orderSource['idr_total_price'] }}
                        </td>
                    @endforeach
                @endforeach
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">{{ __('Total') }}</th>
            <th class="text-right">
                {{ Request::get('action') === 'export' ? $summary['total_quantity'] : $summary['idr_total_quantity'] }}
            </th>
            <th class="text-right">
                {{ Request::get('action') === 'export' ? $summary['total_price'] : $summary['idr_total_price'] }}</th>
            @foreach ($summary['branches'] as $branch)
                @foreach ($branch['order_sources'] as $orderSource)
                    <td class="text-right">
                        {{ Request::get('action') === 'export' ? $orderSource['total_quantity'] : $orderSource['idr_total_quantity'] }}
                    </td>
                    <td class="text-right">
                        {{ Request::get('action') === 'export' ? $orderSource['total_price'] : $orderSource['idr_total_price'] }}
                    </td>
                @endforeach
            @endforeach
        </tr>
    </tfoot>
</table>
